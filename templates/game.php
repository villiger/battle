<?php
/** @var $game \Battle\Game */
$field = $game->getField();
?>

<canvas id="field" class="field"></canvas>

<script>
    (function() {
        Game.init(<?= $game->getStateJson() ?>);
    })();
</script>
