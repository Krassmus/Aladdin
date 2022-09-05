<article class='linked_brainstorm studip<?= $brainstorm->start > time() ? ' inactive' : '' ?>'
         id="brainstorm_<?= $brainstorm->id ?>">
    <header>
        <h1>
            <a href="<?= $controller->url_for('lamp/brainstorm/' . $brainstorm->id) ?>">
            <?= htmlReady($brainstorm->title) ?>
            </a>
        </h1>
        <nav>
            <? if ($brainstorm->start > 0 && $GLOBALS['perm']->have_studip_perm('tutor', $brainstorm['seminar_id'])) : ?>
                <?= sprintf(dgettext("aladdin","(startet am %s Uhr)"), date("d.m.Y G:i", $brainstorm['start'])) ?>
            <? endif ?>
        </nav>
    </header>
    <a href="<?= $controller->url_for('lamp/brainstorm/' . $brainstorm->id) ?>" class="question">

        <div class="body">
            <?= formatReady($brainstorm->text) ?>
        </div>
    </a>
    <? if ($GLOBALS['perm']->have_studip_perm("tutor", Context::get()->id) || $GLOBALS['user']->id === $brainstorm['user_id']) : ?>
        <div class="center">
            <?= \Studip\LinkButton::create(
                dgettext("aladdin","Bearbeiten"),
                PluginEngine::getURL(
                    $plugin,
                    [],
                    "lamp/edit/" . $brainstorm->getId()
                ),
                ['data-dialog' => "size=auto;reload-on-close"]
            ) ?>
            <?= \Studip\LinkButton::create(
                dgettext("aladdin","Exportieren"),
                PluginEngine::getURL(
                    $plugin,
                    [],
                    "lamp/export/" . $brainstorm->getId()
                ),
                ['download' => ($brainstorm['title'] ?: "Aladdin-".$brainstorm->getId()).".csv"]
            ) ?>
            <?= \Studip\LinkButton::create(
                dgettext("aladdin","Löschen"),
                PluginEngine::getURL(
                    $plugin,
                    [],
                    "lamp/delete/" . $brainstorm->getId()
                ),
                [
                    'data-confirm' => dgettext("aladdin","Wirklich löschen?")
                ]
            ) ?>
        </div>
    <? endif ?>
</article>
