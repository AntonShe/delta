<?php
/** @var array $list */
?>
<div class="promotion__swiper swiper">
    <div class="promotion__swiper-wrapper swiper-wrapper">
        <?php foreach($list as $item): ?>
            <div class="promotion__swiper-slide swiper-slide js-banner-analytics" data-banner-id="<?= $item->id ?>" data-banner-name="<?= $item->title ?>" data-banner-slot="<?= $item->sort ?>">
                <?php if($item->link): ?>
                    <a href="<?= $item->link ?>" class="promotion__link">
                <?php endif; ?>
                <?php if($item->image): ?>
                    <div class="promotion__image"
                         style="background-image:url('<?= $item->image ?>')"
                    ></div>
                <?php endif; ?>
                <?php if($item->image): ?>
                    <div class="promotion__image promotion__image_tablet"
                         style="background-image:url('<?= $item->tabletImage ?>')"
                    ></div>
                <?php endif; ?>
                <?php if($item->image): ?>
                    <div class="promotion__image promotion__image_mobile"
                         style="background-image:url('<?= $item->mobileImage ?>')"
                    ></div>
                <?php endif; ?>
                <?php if($item->text): ?>
                    <div class="promotion__text">
                        <?= $item->text ?>
                    </div>
                <?php endif; ?>
                <?php if($item->link): ?>
                    </a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <?php if(count($list) > 3): ?>
        <div class="promotion__swiper-button promotion__swiper-button-prev"></div>
        <div class="promotion__swiper-button promotion__swiper-button-next"></div>
    <?php endif; ?>
</div>