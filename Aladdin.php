<?php

require_once __DIR__."/models/Brainstorm.php";
require_once __DIR__."/models/BrainstormVote.php";

/**
 * BrainstormPlugin.class.php
 *
 * @author  Florian Bieringer <florian.bieringer@uni-passau.de>
 * @version 1.0
 */
class Aladdin extends StudIPPlugin implements StandardPlugin {

    public function __construct()
    {
        parent::__construct();
        if (UpdateInformation::isCollecting()) {
            $data = Request::getArray("page_info");
            if (stripos(Request::get("page"), "plugins.php/aladdin") !== false && isset($data['Aladdin'])) {
                $brainstorm = new Brainstorm($data['Aladdin']['brainstorm_id']);
                if ($GLOBALS['perm']->have_studip_perm("autor", $brainstorm['seminar_id'])) {
                    $newtime = $brainstorm['chdate'];
                    foreach ($brainstorm->children as $child) {
                        if ($child['chdate'] > $newtime) {
                            $newtime = $child['chdate'];
                        }
                        foreach ($child->votes as $vote) {
                            if ($vote['chdate'] > $newtime) {
                                $newtime = $vote['chdate'];
                            }
                        }
                    }

                    if ($newtime > $data['Aladdin']['lasttime']) {
                        $output = array();
                        $tf = new Flexi_TemplateFactory(__DIR__ . "/views");
                        $template = $tf->open("lamp/_subbrainstorms.php");
                        $template->set_attribute("plugin", $this);
                        $template->set_attribute("brainstorm", $brainstorm);
                        $output['html'] = $template->render();
                        $output['lasttime'] = $newtime;
                        UpdateInformation::setInformation("Aladdin.updateSubbrainstorms", $output);
                    }
                }
            }
        }
    }

    public function initialize() {
        self::addStylesheet('/assets/style.less');
        PageLayout::addScript($this->getPluginURL() . '/assets/autoresize.jquery.min.js');
        PageLayout::addScript($this->getPluginURL() . '/assets/application.js');
    }

    public function getTabNavigation($course_id) {
        $navigation = new AutoNavigation($this->getDisplayTitle());
        $navigation->setURL(PluginEngine::GetURL($this, array(), 'lamp/index'));
        $navigation->setActiveImage($this->getPluginURL() . '/assets/images/lightning_black.svg');
        $navigation->setImage($this->getPluginURL() . '/assets/images/lightning_white.svg');
        
        return array('brainstorm' => $navigation);
    }

    public function getNotificationObjects($course_id, $since, $user_id) {
        return array();
    }

    public function getIconNavigation($course_id, $last_visit, $user_id) {
        $new = Brainstorm::findBySQL("seminar_id = ? AND chdate > ?", array($course_id, $last_visit));
        $icon = new Navigation($this->getDisplayTitle(), PluginEngine::GetURL($this, array('cid' => $course_id), 'lamp/index'));
        $icon->setImage($this->getPluginURL() . '/assets/images/lightning_grey.svg', array('title' => $this->getDisplayTitle()));
        if (count($new)) {
            $icon->setImage($this->getPluginURL() . '/assets/images/lightning_red.svg', array('title' => sprintf(_("Neuer %s verfügbar"), $this->getDisplayTitle())));
        }
        return $icon;
    }

    public function getInfoTemplate($course_id) {
        return null;
    }

    public function getDisplayTitle() {
        return _("Aladdin");
    }
}
