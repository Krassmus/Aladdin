<form class="default"
      method="post"
      action="<?= $controller->url_for('lamp/edit/'.$brainstorm->getId()) ?>">

    <?= CSRFProtection::tokenTag() ?>
    
    <input type="hidden" name="brainstorm[range_id]" value="<?= $range_id ?: $brainstorm['range_id'] ?>">

    <? if (!$brainstorm['range_id']) : ?>
    <label>
        <?= dgettext("aladdin",'Titel') ?>
        <input type="text" name="brainstorm[title]" value="<?= htmlReady($brainstorm['title']) ?>" placeholder="<?= dgettext("aladdin","Frage oder Thema") ?>">
    </label>
    <? endif ?>

    <label>
        <?= dgettext("aladdin",'Text') ?>
        <textarea name="brainstorm[text]" placeholder="<?= dgettext("aladdin","Um was soll's gehen?") ?>"><?= htmlReady($brainstorm['text']) ?></textarea>
    </label>
    
    <div data-dialog-button>
        <?= \Studip\Button::create(dgettext("aladdin",'Anlegen'), 'create') ?>
    </div>
</form>