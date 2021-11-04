<article class='brainstorm vote_brainstorm' id="brainstorm_<?= $brainstorm->id ?>" data-brainstorm_id="<?= $brainstorm->id ?>">
    <section class="main">
        <nav>
            <form method="post" class="voting">
                <?= CSRFProtection::tokenTag() ?>
                <input type='hidden' name='brainstorm_id' value='<?= $brainstorm->id ?>'>
                <?= Icon::create("arr_1up", ($brainstorm->myvote->vote == 1 ? "status-green" : "clickable"))->asInput(16, array('name' => 'vote[1][]', 'value' => 1)) ?>
                <?= Icon::create("remove", "clickable")->asInput(16, array('name' => 'vote[0][]', 'value' => 0)) ?>
                <?= Icon::create("arr_1down", ($brainstorm->myvote->vote == -1 ? "status-red" : "clickable"))->asInput(16, array('name' => 'vote[-1][]', 'value' => -1)) ?>
            </form>
        </nav>
        <? if ($brainstorm->title) : ?>
            <h1><?= $brainstorm->title ?></h1>
        <? endif ?>
        <div class='power'>
            <?= $brainstorm->power ?>
        </div>
        <div class="body">
            <? if ($brainstorm['user_id'] === $GLOBALS['user']->id || $GLOBALS['perm']->have_studip_perm("tutor", $brainstorm['seminar_id'])) : ?>
                <div class="actions">
                    <? if ($brainstorm['user_id'] === $GLOBALS['user']->id) : ?>
                    <a href="<?= PluginEngine::getLink($plugin, array('cid' => $brainstorm['seminar_id']), "lamp/edit/".$brainstorm->getId()) ?>" class="edit" data-dialog>
                        <?= Icon::create("edit", "clickable")->asImg(16, array('class' => "text-bottom")) ?>
                    </a>
                    <? endif ?>
                    <a href="<?= PluginEngine::getLink($plugin, array('cid' => $brainstorm['seminar_id']), "lamp/edit/".$brainstorm->getId()) ?>" class="delete">
                        <?= Icon::create("trash", "clickable")->asImg(16, array('class' => "text-bottom")) ?>
                    </a>
                </div>
            <? endif ?>
            <?= formatReady($brainstorm->text) ?>
            <div class="comments">
                <? if (count($brainstorm->children)) : ?>
                    <a href="<?= PluginEngine::getLink($plugin, array('cid' => $brainstorm['seminar_id']), 'lamp/brainstorm/' . $brainstorm->id) ?>" title="<?= dgettext("aladdin","Bester Kommentar dazu") ?>">
                        <?= Icon::create("chat", "clickable")->asImg(14, array('class' => "text-bottom")) ?>
                        <?= formatReady($brainstorm->getBestSubbrainstorm()->text) ?>
                    </a>
                <? else : ?>
                    <a href="<?= PluginEngine::getLink($plugin, array('cid' => $brainstorm['seminar_id']), 'lamp/brainstorm/' . $brainstorm->id) ?>" class="firstcomment" title="<?= dgettext("aladdin","Dazu weiter brainstormen") ?>">
                        <?= Icon::create("comment", "clickable")->asImg(16, array('class' => "text-bottom")) ?>
                    </a>
                <? endif ?>
            </div>
        </div>
    </section>
</article>
