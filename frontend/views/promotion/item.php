<?php

/** @var PromotionDTO $data */

use common\models\promotion\PromotionDTO;

?>
<li class="promotions-page__item">
    <?php if($data->link): ?>
        <a class="promotions-page__promotion" href="<?= $data->link ?>">
    <?php else: ?>
        <div class="promotions-page__promotion">
    <?php endif; ?>
        <div class="promotions-page__text-block">
            <h2 class="promotions-page__name"><?= $data->title ?></h2>
            <p class="promotions-page__description"><?= $data->annotation ?></p>
    <!--        <h3 class="promotions-page__percent">Процент скидки если есть</h3>-->
            <span class="promotions-page__validity"><?= $data->dateValid ?></span>
        </div>
        <div class="promotions-page__graphics">
            <img class="promotions-page__image" src="<?= $data->image ?>" alt="">
            <img class="promotions-page__image promotions-page__image_tablet" src="<?= $data->mobileImage ?>" alt="">
            <img class="promotions-page__image promotions-page__image_mobile" src="<?= $data->tabletImage ?>" alt="">
        </div>
    <?php if($data->link): ?>
        </a>
    <?php else: ?>
        </div>
    <?php endif; ?>
</li>