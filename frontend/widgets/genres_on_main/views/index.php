<?php
/** @var array $list */
?>
<?php if(!empty($list)): ?>
    <div class="promotion__swiper-links swiper">
        <div class="promotion__swiper-links-wrapper swiper-wrapper">
            <?php foreach ($list as $item): ?>
                <a href="/catalog/<?= $item->id ?>" class="promotion__swiper-link-slide swiper-slide button-red">
                    <?= $item->name ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>