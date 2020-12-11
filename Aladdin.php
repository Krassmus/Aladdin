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
        bindtextdomain("aladdin", __DIR__."/locale");
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

                    if ($newtime > Request::get("server_timestamp")) {
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
        $navigation->setActiveImage(Icon::create($this->getPluginURL() . '/assets/images/lightning_black.svg'));
        $navigation->setImage(Icon::create($this->getPluginURL() . '/assets/images/lightning_white.svg'));

        return array('brainstorm' => $navigation);
    }

    public function getNotificationObjects($course_id, $since, $user_id) {
        return array();
    }

    public function getIconNavigation($course_id, $last_visit, $user_id) {
        $new = Brainstorm::countBySQL("seminar_id = ? AND chdate > ? AND user_id != ?", array($course_id, $last_visit, $GLOBALS['user']->id));
        $icon = new Navigation($this->getDisplayTitle(), PluginEngine::GetURL($this, array('cid' => $course_id), 'lamp/index'));
        $icon->setImage(Icon::create($this->getPluginURL() . '/assets/images/lightning_grey.svg', array('title' => $this->getDisplayTitle())));
        if ($new) {
            $icon->setImage(Icon::create($this->getPluginURL() . '/assets/images/lightning_red.svg', array('title' => sprintf(dgettext("aladdin","%s neue Brainstorms verfügbar"), $new))));
        }
        return $icon;
    }

    public function getInfoTemplate($course_id) {
        return null;
    }

    public function getDisplayTitle() {
        return dgettext("aladdin","Aladdin");
    }
    function getMetadata() {
        $metadata = parent::getMetadata();
        $metadata['pluginname'] = dgettext("aladdin", "Aladdin");
        $metadata['displayname'] = dgettext("aladdin", "Aladdin");
        $metadata['descriptionlong'] = dgettext("aladdin", "Mit Aladdin kann man Fragen stellen und schnell Antworten bekommen. Das Besondere ist, dass die Antworten auf- und abgewertet werden, sodass am Ende die sinnvollste Antwort oben steht. Man kann dieses Tool nutzen, um die Komilitonen zu Fragen, was man für die Klausur lernen sollte. Oder welche Maßnahmen bei der ersten Hilfe eine Rolle spielen. Oder man kann einfach fragen, bei welchem Pizzaservice zu einem Blockseminar bestellt werden soll.");
        $metadata['summary'] = dgettext("aladdin", "Textbasiertes Brainstorming-Tool, das an reddit erinnert.");
        return $metadata;
    }
}
