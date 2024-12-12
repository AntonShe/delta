<?php
/** @var array $list */
?>

<?php foreach ($list as $name => $link): ?>
    <?php if(!$link): ?>
        <button class="header__left-item header__left-btn button-black"><?= $name ?></button>
    <?php else: ?>
        <a href="<?= $link ?>" class="header__left-item button-text"><?= $name ?></a>
    <?php endif; ?>
<?php endforeach; ?>