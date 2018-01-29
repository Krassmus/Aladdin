<? if (count($brainstorms)) : ?>
    <div class="brainstorm">
        <? foreach ($brainstorms as $brainstorm): ?>
            <?= $this->render_partial('lamp/_linked_brainstorm', array('brainstorm' => $brainstorm)) ?>
        <? endforeach; ?>
    </div>
<? else : ?>
    <a style="display: flex; align-items: center;" href="<?= PluginEngine::getLink($plugin, array(), "lamp/edit") ?>" data-dialog>
        <img src="<?= $plugin->getPluginURL() ?>/assets/images/lamp.png" style="max-height: 70vh;">
        <div style="font-size: 1.2em;">
            <?= _("Und als verzweifelnd er durchblättert<br>
                Seite für Seite sein Gedächtnis<br>
                Nach Mitteln gegen diese Pein,<br>
                Fiel ihm des falschen Freunds Vermächtnis,<br>
                Die Wunderlampe, wieder ein. ") ?>
        </div>
    </a>
<? endif ?>

<?
$sidebar = Sidebar::Get();

$sidebar->setImage($this->plugin->getPluginURL()."/assets/images/sidebar.png");

// Create actions
$actions = new ActionsWidget();
if ($GLOBALS['perm']->have_studip_perm('tutor', Context::get()->id)) {
    $actions->addLink(
        _('Jetzt brainstormen'),
        PluginEngine::GetURL($plugin, array(), 'lamp/edit'),
        Icon::create('add', "clickable"),
        array('data-dialog' => 'size=auto;buttons=false;resize=false')
    );
}

$sidebar->addWidget($actions);
