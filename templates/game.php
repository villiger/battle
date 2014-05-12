<?php
/** @var $game \Battle\Game */
$field = $game->getField();
?>

<table class="field">
    <tbody>
        <?php for($row = 0; $row < $field->getHeight(); $row++): ?>
            <tr>
                <?php for($column = 0; $column < $field->getWidth(); $column++): ?>
                    <td><?= $field->getTile($row, $column) ?></td>
                <?php endfor; ?>
            </tr>
        <?php endfor; ?>
    </tbody>
</table>

<p>Game ID: <?= $game->getId() ?></p>
