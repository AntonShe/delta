<?php
/** @var array $filters */
?>
<div class="catalog-books__filters-wrap filters__wrap js-filters-block">
    <div class="filters__header-mobile">
        <button class="filters__button-back button-back js-filters-button"></button>
        <div class="filters__title">Фильтры</div>
    </div>
    <div class="catalog-courses__filters filters js-filters-form">
        <?php foreach ($filters as $filter): ?>
            <div class="filter">
                <?= $this->render($filter['template'], $filter) ?>
            </div>
        <?php endforeach; ?>
        <button class="catalog-courses__filters-button filters__button button-red js-filters-submit js-filters-button">
            Применить
        </button>
        <button class="catalog-courses__filters-button_gray filters__button filters__button_grey js-filters-refresh js-filters-button">
            Сбросить все
        </button>
    </div>
</div>