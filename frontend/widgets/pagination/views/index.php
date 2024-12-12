<?php
/** @var int $pageCount */
/** @var int $currentPage */
/** @var int $startPage */
/** @var int $endPage */
?>
<div id="pagination" class="catalog-books__pagination pagination">
    <?php if($pageCount > 1): ?>
    <button class="js-pagination-button pagination__button pagination__button-back button-back
        <?= ($currentPage - 1) < 1 ? ' pagination__button_disabled' : '' ?>"
        data-page="<?= $currentPage - 1 ?>"
    ></button>
    <?php if($startPage > 1): ?>
        <a class="js-pagination-button pagination__number<?= 1 == $currentPage ? ' pagination__number_active' : '' ?>"
           data-page="1"
        >
            1
        </a>
        <div class="pagination__number">...</div>
    <?php endif; ?>
    <?php for($i = $startPage; $i <= $endPage; $i++): ?>
        <a class="js-pagination-button pagination__number<?= $i == $currentPage ? ' pagination__number_active' : '' ?>"
           data-page="<?= $i ?>"
        >
            <?= $i ?>
        </a>
    <?php endfor; ?>
    <?php if($endPage < $pageCount): ?>
        <div class="pagination__number">...</div>
        <a class="js-pagination-button pagination__number<?= $pageCount == $currentPage ? ' pagination__number_active' : '' ?>"
           data-page="<?= $pageCount ?>">
            <?= $pageCount ?>
        </a>
    <?php endif; ?>
    <button class="js-pagination-button pagination__button pagination__button-next button-back
        <?= ($currentPage + 1) > $pageCount ? ' pagination__button_disabled' : '' ?>"
        data-page="<?= $currentPage + 1 ?>"
    ></button>
<?php endif; ?>
</div>
