<?php
/** @var $game \Battle\Game */
/** @var $user \Battle\User */
?>

<canvas id="field" class="field"></canvas>

<div class="sidebar">
    <div>
        <input type="button" class="end-turn btn btn-danger" id="end-turn" value="End Turn">
    </div>
    <div class="chat">
        <form id="chat-form">
            <input class="form-control message-input" id="message" type="text" placeholder="Enter chat message...">
        </form>
        <div id="messages" class="messages"></div>
    </div>
</div>

<div class="clearfix"></div>

<script>
    (function() {
        Game.init(<?= $user->getId() ?>, <?= $game->toJson() ?>);

        var sendMessage = function() {
            var messageElem = $('#message');
            var message = messageElem.val().trim();

            if (message.length > 0) {
                Game.Action.Creators.message(message);
            }

            messageElem.val('');
        };

        $('#chat-form').on('submit', function(event) {
            event.preventDefault();

            sendMessage();
        });

        $('#end-turn').on('click', function() {
            Game.Action.Creators.endTurn();
        });
    })();
</script>
