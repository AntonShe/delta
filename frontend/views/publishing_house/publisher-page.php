<?php
/** @var array $publisher */
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
    <div class="person-page__content">
        <?php if($publisher['cover']): ?>
            <div class="person-page__photo-wrapper person-page__photo-wrapper_publisher">
                <img class="person-page__photo-img person-page__photo-img_publisher" src="<?= $publisher['cover'] ?>" alt="<?= $publisher['name']?>" />
            </div>
        <?php endif; ?>
        <div class="person-page__header">
            <h2 class="person-page__title flex"><?= $publisher['name'] ?></h2>
        </div>
        <div class="person-page__text-wrapper js-container">
            <?php if($publisher['description']): ?>
                <div class="description__text js-show-more-content">
                    <?= $publisher['description'] ?>
                </div>
                <button class="description__btn button-more button-text js-show-more-button" data-text="Развернуть">
                    Развернуть
                </button>
            <?php endif; ?>
        </div>
    </div>
    <div class="person-page__share">
        <?= $this->render('@app/views/share_btn/index.php') ?>
    </div>
</section>

<section class="section container"
         data-personList-id="<?= $publisher['id'] ?>"
         data-personList-name="<?= $publisher['name']?>"
>
    <div class="person-page__subheader">
        <h3 class="person-page__subtitle">Книги издательства</h3>
        <div class="person-page__subshare">
            <?= $this->render('@app/views/share_btn/index.php') ?>
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
