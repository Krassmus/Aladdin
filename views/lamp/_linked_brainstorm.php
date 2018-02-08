<article class='linked_brainstorm' id="brainstorm_<?= $brainstorm->id ?>">
    <a href="<?= $controller->url_for('lamp/brainstorm/' . $brainstorm->id) ?>">
        <h1><?= htmlReady($brainstorm->title) ?></h1>
        <div class="body">
            <?= formatReady($brainstorm->text) ?>
        </div>
    </a>
    <? if ($GLOBALS['perm']->have_studip_perm("tutor", $_SESSION['SessionSeminar']) || $GLOBALS['user']->id === $brainstorm['user_id']) : ?>
        <a class="delete" href="<?= PluginEngine::getLink($plugin, array(), "lamp/delete/".$brainstorm->getId()) ?>" onClick="return window.confirm('<?= _("Wirklich löschen?") ?>');">
            <?= Icon::create("trash", "clickable")->asImg(20) ?>
        </a>
    <? endif ?>
</article>
