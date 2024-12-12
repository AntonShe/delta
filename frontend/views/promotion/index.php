<?php

/** @var yii\web\View $this */
/** @var array $list */

use frontend\widgets\breadcrumbs\BreadcrumbsWidget;

$this->title = 'Акции';
?>

<section class="section container promotions-page">
    <?= BreadcrumbsWidget::widget([
        'template' => BreadcrumbsWidget::BREADCRUMBS_TYPE_PAGE,
        'title' => $this->title,
    ]) ?>

    <div class="promotions-page__flex-block flex">
        <button class="button-back promotions-page__button-back"></button>
        <h1 class="promotions-page__title">
            <?= $this->title?>
        </h1>
    </div>

    <ul class="promotions-page__list">
        <?php foreach($list as $item): ?>
            <?= $this->render('item', ['data' => $item]) ?>
        <?php endforeach; ?>
    </ul>
</section>