<?php
/** @var $game \Battle\Game */
/** @var $user \Battle\User */
?>

<canvas id="field" class="field"></canvas>

<script>
    (function() {
        Game.init(<?= $user->getId() ?>, <?= $game->toJson() ?>);
    })();
</script>
