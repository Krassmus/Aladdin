<?php

class LampController extends PluginController {

    public function before_filter(&$action, &$args) {

        parent::before_filter($action, $args);

        PageLayout::setTitle(Context::getHeaderLine()." - ".$this->plugin->getDisplayTitle());
    }

    public function index_action() {
        Navigation::activateItem("/course/brainstorm");
        $this->brainstorms = Brainstorm::findBySQL("seminar_id = ? AND range_id IS NULL", array(Context::get()->id));
    }

    public function edit_action($brainstorm_id = null) {
        Navigation::activateItem("/course/brainstorm");
        PageLayout::setTitle(dgettext("aladdin","Neuen Brainstorm starten"));

        $data = Request::getArray('brainstorm');
        if ($data['range_id']) {
            $this->parent = new Brainstorm($data['range_id']);
        }

        if ($this->parent || !$GLOBALS['perm']->have_studip_perm("autor", Context::get()->id)) {
            throw new Exception("No permission to post here");
        }

        $this->brainstorm = new Brainstorm($brainstorm_id);
        $edit_mode = !$this->brainstorm->isNew();
        PageLayout::setTitle(
            dgettext(
                'aladdin',
                'Brainstorm bearbeiten'
            )
        );
        if ($edit_mode && !$GLOBALS['perm']->have_studip_perm('tutor', Context::getId())
            && ($this->brainstorm->user_id != $GLOBALS['user']->id)) {
            throw new AccessDeniedException();
        }

        if (Request::isPost() && Request::submitted('create')) {
            CSRFProtection::verifySecurityToken();

            $data['user_id'] = User::findCurrent()->id;
            $data['range_id'] = $this->parent ? $this->parent->getId() : null;
            $data['seminar_id'] = $this->parent ? $this->parent['seminar_id'] : Context::get()->id;

            $this->brainstorm->setData($data);
            $this->brainstorm->store();

            if ($this->parent) {
                $users = array();
                if ($this->parent['user_id'] !== $GLOBALS['user']->id) {
                    $users[] = $this->parent['user_id'];
                }
                foreach ($this->parent->children as $subbrainstorm) {
                    if ($subbrainstorm['user_id'] !== $GLOBALS['user']->id && !in_array($subbrainstorm['user_id'], $users)) {
                        $users[] = $subbrainstorm['user_id'];
                    }
                }
                if (count($users)) {
                    PersonalNotifications::add(
                        $users,
                        PluginEngine::getURL($this->plugin, array(), "lamp/brainstorm/" . $this->parent['id']),
                        sprintf(dgettext("aladdin","%s hat mit gebrainstormed"), get_fullname()),
                        "brainstorm_" . $this->brainstorm->getId(),
                        Avatar::getAvatar($GLOBALS['user']->id)->getURL(Avatar::MEDIUM)
                        //$this->plugin->getPluginURL()."/assets/images/lighning_black.svg"
                    );
                }
            }
            if ($edit_mode && !$this->parent) {
                //Redirect to overview page:
                $this->redirect('lamp/index');
            } else {
                $this->redirect('lamp/brainstorm/'.($this->parent ? $this->parent->getId() : $this->brainstorm->getId()));
            }
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
        if (Request::isAjax()) {
            $this->render_text("ok");
        } else {
            if ($parent) {
                $this->redirect("lamp/brainstorm/" . $parent);
            } else {
                $this->redirect("lamp/index");
            }
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

        $output = array($this->brainstorm['user_id']);

        $users = array();
        foreach ($this->brainstorm->children as $subbrainstorm) {
            if ($subbrainstorm['user_id'] !== $GLOBALS['user']->id && !in_array($subbrainstorm['user_id'], $users)) {
                $users[] = $subbrainstorm['user_id'];
            }
        }

        if (count($users)) {
            PersonalNotifications::add(
                $users,
                PluginEngine::getURL($this->plugin, array(), "lamp/brainstorm/" . $this->brainstorm['id']),
                sprintf(dgettext("aladdin","%s hat mit gebrainstormed"), get_fullname()),
                "brainstorm_" . $this->subbrainstorm->getId(),
                Avatar::getAvatar($GLOBALS['user']->id)->getURL(Avatar::MEDIUM)
                //$this->plugin->getPluginURL()."/assets/images/lighning_black.svg"
            );
        }

        $output['html'] = $this->render_template_as_string("lamp/_subbrainstorms.php");

        $this->render_json($output);
    }

}
