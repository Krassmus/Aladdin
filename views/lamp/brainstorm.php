<div class='brainstorm'>
    <h1><?= htmlReady($brainstorm->title) ?></h1>
    <div class="body">
        <?= formatReady($brainstorm->text) ?>
    </div>

    <hr style="display: block; border: 0px; height: 2px; background-color: #dddddd; width: 50%; margin: 30px; margin-left: auto; margin-right: auto;">

    <? if ($brainstorm->children) : ?>
        <? foreach ($brainstorm->children->orderBy('power DESC, mkdate ASC') as $child): ?>
            <?= $this->render_partial('lamp/_voteable_brainstorm', array('brainstorm' => $child)) ?>
        <? endforeach; ?>
    <? endif ?>

    <? if (!$brainstorm['closed']) : ?>
        <form class='default' method='post' action="<?= $controller->url_for('lamp/edit') ?>">
            <?= CSRFProtection::tokenTag() ?>
            <input type="hidden" name="range_id" value="<?= $brainstorm->getId() ?>">
            <textarea type='text' name='brainstorm[text]' rows='0' cols='30' placeholder="Brainstorming ..."></textarea>
            <?= \Studip\Button::create(_('Absenden'), 'create') ?>
        </form>
    <? endif ?>
</div>