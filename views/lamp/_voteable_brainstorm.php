<article class='brainstorm vote_brainstorm' id="brainstorm_<?= $brainstorm->id ?>" data-brainstorm_id="<?= $brainstorm->id ?>">
    <section class="main">
        <nav>
            <form method="post" class="voting">
                <?= CSRFProtection::tokenTag() ?>
                <input type='hidden' name='brainstorm_id' value='<?= $brainstorm->id ?>'>
                <?= Assets::input('icons/16/' . ($brainstorm->myvote->vote == 1 ? 'green' : 'blue') . '/arr_1up.png', array('name' => 'vote[1][]', 'value' => 1)); ?>
                <?= Assets::input('icons/16/blue/remove.png', array('name' => 'vote[0][]', 'value' => 0)); ?>
                <?= Assets::input('icons/16/' . ($brainstorm->myvote->vote == -1 ? 'red' : 'blue') . '/arr_1down.png', array('name' => 'vote[-1][]', 'value' => -1)); ?>
            </form>
        </nav>
        <h1><?= htmlReady($brainstorm->title) ?></h1>
        <div class='power'>
            <?= $brainstorm->power ?>
        </div>
        <div class="body">
            <?= formatReady($brainstorm->text) ?>
            <div class="comments">
                <? if (count($brainstorm->children)) : ?>
                    <a href="<?= $controller->url_for('lamp/brainstorm/' . $brainstorm->id) ?>" title="<?= _("Bester Kommentar dazu") ?>">
                        <?= Assets::img("icons/16/blue/chat", array('class' => "text-bottom")) ?>
                        <?= formatReady($brainstorm->getBestSubbrainstorm()->text) ?>
                    </a>
                <? else : ?>
                    <a href="<?= $controller->url_for('lamp/brainstorm/' . $brainstorm->id) ?>" class="firstcomment" title="<?= _("Dazu weiter brainstormen") ?>">
                        <?= Assets::img("icons/16/blue/comment") ?>
                    </a>
                <? endif ?>
            </div>
        </div>
    </section>
</article>