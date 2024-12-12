<?php
/** @var AbstractDTO $genreInfo */
/** @var array $list */
/** @var array $pagination */
/** @var array $popularList */
/** @var array $params */
/** @var array $parentGenre */

use common\models\AbstractDTO;
use frontend\assets\GenreAsset;
use frontend\widgets\filters\FiltersWidget;
use frontend\widgets\breadcrumbs\BreadcrumbsWidget;

GenreAsset::register($this);
?>
<section class="section container popular">
    <?= BreadcrumbsWidget::widget([
        'template' => BreadcrumbsWidget::BREADCRUMBS_TYPE_CATALOG_GENRES,
        'title' => $genreInfo->name,
    ]) ?>

    <?php if(!empty($popularList)): ?>
        <?= $this->render('_popular-genre', [
                'backUrl' => $genreInfo->level > 2 ? "/catalog/{$parentGenre['id']}" : '',
                'list' => $popularList
        ]) ?>
    <?php endif; ?>
</section>

<section class="section container catalog-courses">

    <h1 class="catalog-courses__title">
        <?= $genreInfo->name ?>
    </h1>

    <?php $filters = FiltersWidget::begin([
        'type' => FiltersWidget::FILTERS_TYPE_COURSES,
        'params' => $params,
        'genreId' => $genreInfo->id,
    ]); ?>
    <?= $filters->renderOpenButton() ?>
    <div class="catalog-courses__wrapper flex">
        <?= $filters->renderIndex() ?>
        <?php FiltersWidget::end(); ?>

        <div class="catalog-courses__block-wrap">
            <div class="catalog-courses__block flex">
                <?php foreach ($list as $item): ?>
                    <?= $this->render('_genre', ['data' => $item]) ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>