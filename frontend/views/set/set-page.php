<?php
/** @var string $setName */
/** @var array $pagination */
/** @var array $list */


use frontend\assets\ProductAsset;
use frontend\widgets\filters\FiltersAsset;
use frontend\widgets\pagination\PaginationWidget;
use frontend\widgets\sorting\SortingAsset;

ProductAsset::register($this);
FiltersAsset::register($this);
SortingAsset::register($this);
?>

<section class="section container person-page">
    <div class="person-page__content person-page__content_series">
        <div class="person-page__header flex">
            <button onclick="history.back()" class="button-back"></button>
            <h3 class="person-page__title-series"><?= $setName ?></h3>
        </div>
    </div>
    <div class="person-page__share">
        <?= $this->render('@app/views/share_btn/index.php') ?>
    </div>
</section>

<section class="section container""
         data-personList-name="<?= $setName ?>"
>
    <div class="person-page__subheader">
        <h3 class="person-page__subtitle">Книги подборки</h3>
        <div class="person-page__subshare">
            <?= $this->render('@app/views/share_btn/index.php', ['notext' => true ]) ?>
        </div>
    </div>
    <div class="js-tabs">
        <div class="books-catalog__tabs-body">
            <div class="catalog-books__grid grid">
                <?php foreach ($list as $key=>$item): ?>
                    <?= $this->render('@app/views/product_cards/default', ['data' => $item, 'index' => $key]) ?>
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
