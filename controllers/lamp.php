<?php

class LampController extends PluginController {

    public function before_filter(&$action, &$args) {

        parent::before_filter($action, $args);

        PageLayout::setTitle($GLOBALS['SessSemName']["header_line"]." - ".$this->plugin->getDisplayTitle());
        $this->init();
    }

    public function index_action() {
        Navigation::activateItem("/course/brainstorm");
        $this->brainstorms = Brainstorm::findBySQL("seminar_id = ? AND range_id IS NULL", array($_SESSION['SessionSeminar']));
    }

    public function edit_action($brainstorm_id = null) {
        Navigation::activateItem("/course/brainstorm");
        PageLayout::setTitle(_("Neuen Brainstorm starten"));

        if (Request::option("range_id")) {
            $this->parent = new Brainstorm(Request::option("range_id"));
        }

        if (($this->parent && !$GLOBALS['perm']->have_studip_perm("autor", $this->parent['seminar_id'])) || !$GLOBALS['perm']->have_studip_perm("autor", $_SESSION['SessionSeminar'])) {
            throw new Exception("No permission to post here");
        }

        $this->brainstorm = new Brainstorm($brainstorm_id);

        if (Request::isPost() && Request::submitted('create')) {
            CSRFProtection::verifySecurityToken();
            $data = Request::getArray('brainstorm');
            $data['user_id'] = User::findCurrent()->id;
            $data['range_id'] = $this->parent ? $this->parent->getId() : null;
            $data['seminar_id'] = $this->parent ? $this->parent['seminar_id'] : $_SESSION['SessionSeminar'];

            $this->brainstorm = Brainstorm::create($data);
            if ($this->parent) {
                foreach ($this->parent->children as $subbrainstorm) {
                    if ($subbrainstorm['user_id'] !== $GLOBALS['user']->id) {
                        PersonalNotifications::add(
                            $subbrainstorm['user_id'],
                            PluginEngine::getURL($this->plugin, array(), "lamp/brainstorm/".$this->parent['id']),
                            sprintf(_("%s hat mit gebrainstormed"), get_fullname()),
                            "brainstorm_".$this->brainstorm->getId(),
                            Avatar::getAvatar($GLOBALS['user']->id)->getURL(Avatar::MEDIUM)
                            //$this->plugin->getPluginURL()."/assets/images/lighning_black.svg"
                        );
                    }
                }
            }
            $this->redirect('lamp/brainstorm/'.($this->parent ? $this->parent->getId() : $this->brainstorm->getId()));
        }
    }

    public function brainstorm_action($id) {
        Navigation::activateItem("/course/brainstorm");

        $this->brainstorm = new Brainstorm($id);

        // Insert new subbrainstorm
        if (Request::isPost() && Request::submitted('create')) {
            CSRFProtection::verifySecurityToken();
            $this->brainstorm->answer(Request::get('answer'));
        }

        // Check if vote is required
        if (Request::isPost() && Request::submitted('vote')) {
            CSRFProtection::verifySecurityToken();
            $brainstorm = new Brainstorm(Request::get('brainstorm_id'));
            $brainstorm->vote(key(Request::getArray('vote')));
        }
    }

    public function delete_action($brainstorm_id)
    {
        $this->brainstorm = new Brainstorm($brainstorm_id);
        if (($GLOBALS['user']->id !== $this->brainstorm['user_id']) && !$GLOBALS['perm']->have_studip_perm("tutor", $this->brainstorm['seminar_id'])) {
            throw new Exception("No rights to delete.");
        }
        $parent = $this->brainstorm['range_id'];
        $this->brainstorm->delete();
        PageLayout::postMessage(MessageBox::success(_("Beitrag wurde gelöscht.")));
        if ($parent) {
            $this->redirect("lamp/brainstorm/".$parent);
        } else {
            $this->redirect("lamp/index");
        }
    }

    public function vote_action($brainstorm_id)
    {
        $this->subbrainstorm = new Brainstorm($brainstorm_id);
        if (($GLOBALS['user']->id !== $this->subbrainstorm['user_id']) && !$GLOBALS['perm']->have_studip_perm("autor", $this->subbrainstorm['seminar_id'])) {
            throw new Exception("No rights to vote.");
        }
        $this->subbrainstorm->vote(Request::get("value"));

        $this->brainstorm = $this->subbrainstorm->parent;

        $output = array(
            'html' => $this->render_template_as_string("lamp/_subbrainstorms.php")
        );

        $this->render_json($output);
    }

    public function add_subbrainstorm_action($brainstorm_id)
    {
        $this->brainstorm = new Brainstorm($brainstorm_id);
        if (!$GLOBALS['perm']->have_studip_perm("autor", $this->brainstorm['seminar_id'])) {
            throw new Exception("No rights to vote.");
        }
        $this->subbrainstorm = new Brainstorm();
        $this->subbrainstorm['seminar_id'] = $this->brainstorm['seminar_id'];
        $this->subbrainstorm['range_id'] = $this->brainstorm->getId();
        $this->subbrainstorm['user_id'] = $GLOBALS['user']->id;
        $this->subbrainstorm['text'] = Request::get("text");
        $this->subbrainstorm->store();

        $output = array(
            'html' => $this->render_template_as_string("lamp/_subbrainstorms.php")
        );

        $this->render_json($output);
    }

    private function init()
    {
        // Fetch sidebar
        $sidebar = Sidebar::Get();

        $sidebar->setImage($this->plugin->getPluginURL()."/assets/images/sidebar.png");

        // Create actions
        $actions = new ActionsWidget();
        if ($GLOBALS['perm']->have_studip_perm('tutor', Course::findCurrent()->id)) {
            $actions->addLink(_('Jetzt brainstormen'), PluginEngine::GetURL($this->plugin, array(), 'lamp/edit'), 'icons/16/blue/add.png', array('data-dialog' => 'size=auto;buttons=false;resize=false'));
        }

        $sidebar->addWidget($actions);
    }
}
