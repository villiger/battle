<?php
/** @var $game \Battle\Game */
$field = $game->getField();
?>

<table class="field">
    <tbody>
        <?php for($row = 0; $row < $field->getHeight(); $row++): ?>
            <tr>
                <?php for($column = 0; $column < $field->getWidth(); $column++): ?>
                    <td class="tile <?= $field->getTileType($row, $column) ?>">
                        <div class="sizer"></div>
                    </td>
                <?php endfor; ?>
            </tr>
        <?php endfor; ?>
    </tbody>
</table>
