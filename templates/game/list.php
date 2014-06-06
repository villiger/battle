<h1>Games</h1>

<?php
// Where should we put this function? o.O
function time_since($since) {
    $chunks = array(
        array(60 * 60 * 24 * 365 , 'year'),
        array(60 * 60 * 24 * 30 , 'month'),
        array(60 * 60 * 24 * 7, 'week'),
        array(60 * 60 * 24 , 'day'),
        array(60 * 60 , 'hour'),
        array(60 , 'minute'),
        array(1 , 'second')
    );

    for ($i = 0, $j = count($chunks); $i < $j; $i++) {
        $seconds = $chunks[$i][0];
        $name = $chunks[$i][1];
        if (($count = floor($since / $seconds)) != 0) {
            break;
        }
    }

    $print = ($count == 1) ? '1 '.$name : "$count {$name}s";
    return $print;
}

?>

<ul class="games">
    <?php foreach ($games as $game): /** @var $game \Battle\Game */ ?>
        <?php $opponent = $game->getOpponent($currentUser); ?>
        <?php //$lastAction = date(DATE_ATOM, $game->getLastAction()->getCreated()); ?>
        <li>
            <?php if ($opponent): ?>
                <img class="picture" src="<?= $opponent->getPicture() ?>">
            <?php endif; ?>
            <a href="/game/<?= $game->getId() ?>"><?= $opponent ? $opponent->getName() : '<i>Open game</i>' ?></a>
            <i><?= time_since(time() - strtotime($game->getLastAction()->getCreated())) ?> ago</i>
        </li>
    <?php endforeach; ?>
</ul>
