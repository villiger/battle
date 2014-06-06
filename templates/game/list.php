<h1>Games</h1>

<ul class="games">
    <?php foreach ($games as $game): /** @var $game \Battle\Game */ ?>
        <?php $opponent = $game->getOpponent($currentUser); ?>
        <li>
            <?php if ($opponent): ?>
                <img class="picture" src="<?= $opponent->getPicture() ?>">
            <?php endif; ?>
            <a href="/game/<?= $game->getId() ?>"><?= $opponent ? $opponent->getName() : '<i>Open game</i>' ?></a>
        </li>
    <?php endforeach; ?>
</ul>
