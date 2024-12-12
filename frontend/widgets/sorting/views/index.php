<?php
/** @var array $list */
/** @var string $current */
?>
<div class="sorting" id="sorting">
    <ul class="sorting__list">
        <?php foreach($list as $key => $item) : ?>
            <li class="sorting__item js-sorting-button<?= $key == $current ? ' sorting__item_active' : '' ?>"
                data-sort="<?= $item['sort'] ?>"
                data-order="<?= $item['order'] ?>"
                data-name="<?= $key ?>"
            >
                <?= $item['text'] ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>