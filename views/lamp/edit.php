<form class="default"
      method="post"
      action="<?= $controller->url_for('lamp/edit/'.$brainstorm->getId()) ?>">

    <?= CSRFProtection::tokenTag() ?>

    <input type="hidden" name="brainstorm[range_id]" value="<?= isset($range_id) ? $range_id : $brainstorm['range_id'] ?>">

    <fieldset>
        <legend>
            <?= _("Fragestellung") ?>
        </legend>
        <? if (!$brainstorm['range_id']) : ?>
            <label>
                <?= dgettext("aladdin",'Titel') ?>
                <input type="text"
                       name="brainstorm[title]"
                       required
                       value="<?= htmlReady($brainstorm['title']) ?>"
                       placeholder="<?= dgettext("aladdin","Frage oder Thema") ?>">
            </label>
        <? endif ?>

        <label>
            <?= dgettext("aladdin",'Text') ?>
            <textarea name="brainstorm[text]"
                      required
                      placeholder="<?= dgettext("aladdin","Um was soll's gehen?") ?>"><?= htmlReady($brainstorm['text']) ?></textarea>
        </label>
    </fieldset>

    <fieldset>
        <legend>
            <?= _("Metadaten") ?>
        </legend>
        <div class="hgroup">
            <label>
                <?= _("Startet am") ?>
                <input type="text"
                       name="brainstorm[start]"
                       class="aladdin-datetimepicker"
                       value="<?= $brainstorm['start'] ? date("d.m.Y H:i", $brainstorm['start']) : "" ?>">
            </label>
        </div>
    </fieldset>

    <div data-dialog-button>
        <? if ($brainstorm->isNew()): ?>
            <?= \Studip\Button::create(dgettext("aladdin",'Anlegen'), 'create') ?>
        <? else: ?>
            <?= \Studip\Button::create(dgettext("aladdin",'Speichern'), 'create') ?>
        <? endif ?>
    </div>
</form>
