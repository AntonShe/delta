<?php
/** @var AbstractDTO $genreInfo */
/** @var array $parentGenre */
/** @var array $list */
/** @var array $levels */
/** @var array $params */

use common\models\AbstractDTO;
use frontend\assets\GenreAsset;
use frontend\widgets\filters\FiltersWidget;
use frontend\widgets\breadcrumbs\BreadcrumbsWidget;

GenreAsset::register($this);
?>
<section class="section container course-about js-container">
    <?= BreadcrumbsWidget::widget([
        'template' => BreadcrumbsWidget::BREADCRUMBS_TYPE_GENRE,
        'title' => $genreInfo->name,
        'parentGenre' => $parentGenre,
    ]) ?>

    <div class="page-top__title-wrap flex">
        <?php if($genreInfo->level > 2): ?>
            <a href="/catalog/<?= $parentGenre['id'] ?>" class="page-top__title-link button-back"></a>
        <?php endif; ?>
        <h1 class="page-top__title">
            <?= $genreInfo->name ?>
        </h1>
    </div>

    <div class="description__text js-show-more-content">
        <?= $genreInfo->getPreparedDescription(); ?>
    </div>
    <button class="description__btn button-more button-text js-show-more-button" data-text="Развернуть">
        Развернуть
    </button>
</section>

<section class="section container books-catalog js-books-catalog"
         data-productList-id="<?= $genreInfo->id ?>"
         data-productList-name="<?= $genreInfo->name ?>"
>
    <div class="books-catalog__tabs js-tabs">
        <nav>
            <ul class="books-catalog__tabs-head flex">
                <li class="books-catalog__tabs-caption books-catalog__tabs-caption_active js-tabs-button" data-id="all">
                    Все товары
                </li>
                <?php foreach($levels as $level): ?>
                    <li class="books-catalog__tabs-caption js-tabs-button"
                        data-id="<?= $level['id'] ?>"
                    >
                        <?= $level['name'] ?>
                    </li>
                <?php endforeach; ?>
                <?php if(count($levels) > 0): ?>
                    <li class="books-catalog__tabs-caption js-tabs-button" data-id="rest">
                        Остальное
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
        <div class="books-catalog__tabs-body">
            <div class="books-catalog__tabs-content books-catalog__tabs-content_active js-tabs-content" data-id="all">
                <div class="grid">
                    <?php foreach ($list as $key=>$item): ?>
                        <?= $this->render('@app/views/product_cards/default', ['data' => $item, 'index' => $key, 'list_id' => $genreInfo->id, 'list_name' => $genreInfo->name]) ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php foreach($levels as $level): ?>
                <div class="books-catalog__tabs-content js-tabs-content" data-id="<?= $level['id'] ?>">
                    <div class="grid">
                        <?php foreach ($list as $item): ?>
                            <?php if(in_array($level['id'], array_column($item->levels, 'id'))): ?>
                                <?= $this->render('@app/views/product_cards/default', ['data' => $item]) ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if(count($levels) > 0): ?>
                <div class="books-catalog__tabs-content js-tabs-content" data-id="rest">
                    <div class="grid">
                        <?php foreach ($list as $item): ?>
                            <?php if(!$item->levels): ?>
                                <?= $this->render('@app/views/product_cards/default', ['data' => $item]) ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
      </div>
    </div>
</section>