<?php
/** @var array $list */
/** @var array $backUrl */
?>
<div class="popular__wrapper">
    <div class="page-top__title-wrap flex">
        <?php if($backUrl): ?>
            <a href="<?= $backUrl ?>" class="page-top__title-link button-back"></a>
        <?php endif; ?>
        <h2 class="popular__title">
            Популярные курсы
        </h2>
    </div>

    <div class="swiper popular__swiper">
        <div class="popular__swiper-button button-swiper button-swiper-prev"></div>
        <div class="popular__swiper-button button-swiper button-swiper-next"></div>
        <div class="popular__swiper-wrapper swiper-wrapper">
            <?php foreach ($list as $item): ?>
                <div class="popular__swiper-slide swiper-slide">
                    <div class="popular__item popular__item_bg_indigo">
                        <a href="<?= $item->url ?>" class="popular__link"></a>
                        <div class="popular__item-left">
                            <div class="popular__item-title">
                                <?= $item->name ?>
                            </div>
                            <div class="popular__item-descr">
                                <?= $item->smallDescription ?>
                            </div>
                        </div>
                        <div class="popular__item-right popular__item-right_course">
                            <div class="poppular__img-wrap">
                                <div class="popular__item-img">
                                    <img src="<?= $item->images[0] ?: '/img/empty.png' ?>" alt="<?= $item->name ?>">
                                </div>
                                <div class="popular__item-img">
                                    <img src="<?= $item->images[1] ?: '/img/empty.png' ?>" alt="<?= $item->name ?>">
                                </div>
                            </div>
                            <div class="poppular__img-wrap">
                                <div class="popular__item-img">
                                    <img src="<?= $item->images[2] ?: '/img/empty.png' ?>" alt="<?= $item->name ?>">
                                </div>
                                <div class="popular__item-img">
                                    <img src="<?= $item->images[3] ?: '/img/empty.png' ?>" alt="<?= $item->name ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>