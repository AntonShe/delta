<?php
/** @var AbstractDecorator $data */

use common\models\AbstractDecorator;
?>
<div class="popular__item popular__item_bg_indigo">
    <a href="<?= $data->url ?>" class="popular__link"></a>
    <div class="popular__item-left">
        <div class="popular__item-title">
            <?= $data->title ?>
        </div>
        <div class="popular__item-descr">
            <?= $data->shortAnnotation ?>
        </div>
    </div>
    <div class="popular__item-right popular__item-right_book">
        <div class="popular__item-img">
            <img src="<?= $data->cover ?: '/img/empty.png' ?>" alt="<?= $data->title ?>">
        </div>
    </div>
</div>