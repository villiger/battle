<h1>Friends</h1>

<ul class="friends">
    <?php foreach ($friends as $friend): /** @var $friend \Battle\User */ ?>
        <li>
            <form method="post" action="/game">
                <img class="picture" src="<?= $friend->getPicture() ?>">
                <span class="name"><?= $friend->getName() ?></span>
                <input class="play btn btn-primary" type="submit" value="Play">
                <input type="hidden" name="opponent" value="<?= $friend->getId() ?>">
            </form>
        </li>
    <?php endforeach; ?>
</ul>
