<? if ($brainstorm->parent) : ?>
    <a href="<?= PluginEngine::getLink($plugin, array(), "lamp/brainstorm/".$brainstorm->parent->getId()) ?>" class="parent_brainstorm">
        <div class="title"><?= htmlReady($brainstorm->parent->title) ?></div>
        <div class="body">
            <?= formatReady($brainstorm->parent->text) ?>
        </div>
    </a>
<? else : ?>
    <a href="<?= PluginEngine::getLink($plugin, array(), "lamp/index") ?>">
        <?= Icon::create("arr_1up", "clickable")->asImg(20, array("class" => "text-bottom")) ?>
        <?= dgettext("aladdin","Zur Übersicht") ?>
    </a>
<? endif ?>
<div class='brainstorm'>
    <div class="brainstorm_body">
        <? if ($brainstorm->title) : ?>
            <h1><?= htmlReady($brainstorm->title) ?></h1>
        <? endif ?>
        <div class="body">
            <?= formatReady($brainstorm->text) ?>
        </div>
    </div>

    <hr style="display: block; border: 0px; height: 2px; background-color: #dddddd; width: 50%; margin: 30px; margin-left: auto; margin-right: auto;">

    <?= $this->render_partial("lamp/_subbrainstorms.php") ?>

    <? if (!$brainstorm['closed']) : ?>
        <form class='default' method='post' action="<?= $controller->url_for('lamp/edit') ?>" onSubmit="STUDIP.Aladdin.postBrainstorm.call(this); return false;">
            <?= CSRFProtection::tokenTag() ?>
            <input type="hidden" name="range_id" value="<?= $brainstorm->getId() ?>">
            <textarea type='text' name='brainstorm[text]' rows='0' cols='30' placeholder="Brainstorming ..."></textarea>
            <?= \Studip\Button::create(dgettext("aladdin",'Absenden'), 'create') ?>
        </form>
    <? endif ?>
</div>

<?
$sidebar = Sidebar::Get();

$sidebar->setImage($this->plugin->getPluginURL()."/assets/images/sidebar.png");

// Create actions
$actions = new ActionsWidget();
if ($GLOBALS['perm']->have_studip_perm('tutor', Context::get()->id)) {
    $actions->addLink(
        dgettext("aladdin",'Jetzt brainstormen'),
        PluginEngine::GetURL($plugin, array(), 'lamp/edit'),
        Icon::create('add', "clickable"),
        array('data-dialog' => 'size=auto;buttons=false;resize=false')
    );
    $oldbase = URLHelper::setBaseURL($GLOBALS['ABSOLUTE_URI_STUDIP']);
    $actions->addLink(
        dgettext("aladdin","QR-Code anzeigen"),
        PluginEngine::getURL($plugin, array(), "lamp/brainstorm/".$brainstorm->id),
        Icon::create("code-qr", "clickable"),
        array('data-qr-code' => "1")
    );
    URLHelper::setBaseURL($oldbase);
}

$sidebar->addWidget($actions);