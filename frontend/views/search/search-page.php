<?php
/** @var array $list */
/** @var array $pagination */
/** @var array $popularList */
/** @var array $params */

use frontend\assets\ProductAsset;
use frontend\widgets\filters\FiltersWidget;
use frontend\widgets\pagination\PaginationWidget;
use frontend\widgets\breadcrumbs\BreadcrumbsWidget;
use frontend\widgets\sorting\SortingWidget;

ProductAsset::register($this);
?>
<section class="section container popular">
    <?= BreadcrumbsWidget::widget([
        'template' => BreadcrumbsWidget::BREADCRUMBS_TYPE_SEARCH
    ]) ?>

    <?php if(!empty($popularList)): ?>
        <?= $this->render('@app/views/catalog/_popular', [
                'list' => $popularList,
                'title' => 'Популярные книги',
        ]) ?>
    <?php endif; ?>
</section>

<section class="section container catalog-books js-catalog-books"
         data-productList-id="search"
         data-productList-name="<?= $params['search'] ?>"
>
    <div class="catalog-books__title-container">
        <h1 class="catalog-books__title">
            Поиск по тексту "<?= $params['search'] ?>"
        </h1>
        <?= SortingWidget::widget(['params' => $params]); ?>
    </div>


    <?php $filters = FiltersWidget::begin([
        'type' => FiltersWidget::FILTERS_TYPE_SEARCH,
        'params' => $params,
    ]); ?>
    <?= $filters->renderOpenButton() ?>
    <div class="catalog-books__wrapper flex">
        <?= $filters->renderIndex() ?>
        <?php FiltersWidget::end(); ?>

        <div class="catalog-books__block-wrap">
            <div class="catalog-books__grid grid">
                <?php foreach ($list as $key=>$item): ?>
                    <?= $this->render('@app/views/product_cards/default', ['data' => $item, 'index' => $key, 'list_id' => "search", 'list_name' => $params['search']]) ?>
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
