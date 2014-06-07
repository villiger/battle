<?php
/** @var $game \Battle\Game */
/** @var $user \Battle\User */
?>

<canvas id="field" class="field"></canvas>

<div class="chat">
    <form>
        <input class="form-control message-input" id="message" type="text">
        <input class="btn btn-primary" id="send-message" type="button" value="Send">
    </form>
    <div id="messages" class="messages"></div>
</div>

<div class="clearfix"></div>

<script>
    (function() {
        Game.init(<?= $user->getId() ?>, <?= $game->toJson() ?>);

        $('#send-message').on('click', function() {
            var messageElem = $('#message');
            var message = messageElem.val();

            Game.Action.Creators.message(message);

            messageElem.val('');
        });
    })();
</script>
