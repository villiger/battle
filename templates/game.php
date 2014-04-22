<h1>Game ID: <?= $game->gameId ?></h1>

<?php $field = $game->getField(); ?>

<form method="POST" action="/game/<?= $game->gameId ?>">

  <?php foreach($field as $rowKey=>$row): ?>
  <div class="row">
    <?php foreach($row as $colKey=>$cell): ?>
      <div class="col-xs-2" id="cell_<?= $rowKey ?>_<?= $colKey ?>">
        <input type="text" name="cell_<?= $rowKey ?>_<?= $colKey ?>" value="<?=$cell?>"/>
      </div>
    <?php endforeach ?>
  </div>
  <?php endforeach ?>


  <input type="hidden" name="field_size" value="<?= \Battle\Field::FIELD_SIZE ?>"/>
  <input type="submit" value="Save!"/>
</form>

<div ng-view></div>
