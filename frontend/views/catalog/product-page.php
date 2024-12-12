<?php
/** @var AbstractDTO $genreInfo */
/** @var array $list */
/** @var array $pagination */
/** @var array $popularList */
/** @var array $params */

use common\models\AbstractDTO;
use frontend\assets\ProductAsset;
use frontend\widgets\filters\FiltersWidget;
use frontend\widgets\pagination\PaginationWidget;
use frontend\widgets\breadcrumbs\BreadcrumbsWidget;
use frontend\widgets\sorting\SortingWidget;

ProductAsset::register($this);
?>
<section class="section container popular">
    <?= BreadcrumbsWidget::widget([
        'template' => BreadcrumbsWidget::BREADCRUMBS_TYPE_CATALOG_PRODUCTS,
        'title' => $genreInfo->name,
        'product' => $list[0],
        'level' => $genreInfo->level,
    ]) ?>

    <?php if(!empty($popularList)): ?>
        <?= $this->render('_popular', ['list' => $popularList]) ?>
    <?php endif; ?>
</section>

<section class="section container catalog-books js-catalog-books"
         data-productList-id="<?= $genreInfo->id ?>"
         data-productList-name="<?= $genreInfo->name ?>"
>
    <div class="catalog-books__title-container">
        <h1 class="catalog-books__title">
            <?= $genreInfo->name ?>
        </h1>
        <?= SortingWidget::widget(['params' => $params]); ?>
    </div>

    <?php $filters = FiltersWidget::begin([
        'type' => FiltersWidget::FILTERS_TYPE_PRODUCTS,
        'params' => $params,
        'genreId' => $genreInfo->id,
    ]); ?>
    <?= $filters->renderOpenButton() ?>
    <div class="catalog-books__wrapper flex">
        <?= $filters->renderIndex() ?>
        <?php FiltersWidget::end(); ?>

        <div class="catalog-books__block-wrap">
            <div class="catalog-books__grid grid">
                <?php foreach ($list as $key=>$item): ?>
                    <?= $this->render('@app/views/product_cards/default', ['data' => $item, 'index' => $key, 'list_id' => $genreInfo->id, 'list_name' => $genreInfo->name]) ?>
                <?php endforeach; ?>
            </div>

            <?= PaginationWidget::widget([
                'pageCount' => $pagination['pageCount'],
                'currentPage' => $pagination['currentPage'],
                'startPage' => $pagination['startPage'],
                'endPage' => $pagination['endPage'],
            ]); ?>
        </div>
    </div>
</section>
