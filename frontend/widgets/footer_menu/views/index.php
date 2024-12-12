<?php
/** @var array $list */
?>
<?php foreach ($list as $categoryName => $items): ?>
    <div class="footer__nav-items footer__top-item">
        <ul class="footer__nav-list">
            <?php foreach ($items as $name => $link): ?>
                <li class="footer__nav-item">
                    <a href="<?= $link ?>" class="footer__nav-item-link"><?= $name ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endforeach; ?>