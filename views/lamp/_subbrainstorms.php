<div class="subbrainstorms">
    <? if ($brainstorm->children) : ?>
        <? foreach ($brainstorm->children->orderBy('power DESC, mkdate ASC') as $child): ?>
            <?= $this->render_partial('lamp/_voteable_brainstorm', array('brainstorm' => $child)) ?>
        <? endforeach; ?>
    <? endif ?>
</div>