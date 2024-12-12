<?php
/** @var bool $productId */

use frontend\assets\RatingAsset;

RatingAsset::register($this);
?>
<?php if(!Yii::$app->user->isGuest): ?>
    <div class="big-card__main-bottom">
        <p class="big-card__main-descr">Ваша оценка поможет другим сделать выбор</p>
        <div class="big-card__rating">
            <div class="rating" data-id="<?= $productId ?>">
                <?php for($i = 5; $i >= 1; $i--): ?>
                    <span class="rating__star" data-value="<?= $i ?>"></span>
                <?php endfor; ?>
            </div>
        </div>
    </div>
<?php endif; ?>