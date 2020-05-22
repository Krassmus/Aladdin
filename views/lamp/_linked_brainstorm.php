<article class='linked_brainstorm' id="brainstorm_<?= $brainstorm->id ?>">
    <div style="float: right;">
        <?= date("G:i d.m.Y", $brainstorm['mkdate']) ?>
    </div>
    <a href="<?= $controller->url_for('lamp/brainstorm/' . $brainstorm->id) ?>">
        <h1><?= htmlReady($brainstorm->title) ?></h1>
        <div class="body">
            <?= formatReady($brainstorm->text) ?>
        </div>
    </a>
    <? if ($GLOBALS['perm']->have_studip_perm("tutor", Context::get()->id) || $GLOBALS['user']->id === $brainstorm['user_id']) : ?>
        <a class="edit"
           href="<?= PluginEngine::getLink(
                 $plugin,
                 [],
                 "lamp/edit/" . $brainstorm->getId()
                 ) ?>" data-dialog="size=auto;reload-on-close">
            <?= Icon::create("edit", "clickable")->asImg(20) ?>
        </a>
        <a class="delete" href="<?= PluginEngine::getLink($plugin, array(), "lamp/delete/".$brainstorm->getId()) ?>" onClick="return window.confirm('<?= dgettext("aladdin","Wirklich löschen?") ?>');">
            <?= Icon::create("trash", "clickable")->asImg(20) ?>
        </a>
    <? endif ?>
</article>
