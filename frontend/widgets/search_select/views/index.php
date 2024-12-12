<?php
/** @var yii\web\View $this */
/** @var string $id */
/** @var array $items */
/** @var string $placeholder */
/** @var string $additionalClasses */
?>
<div class="search-select">
    <input class="search-select__input input <?=$additionalClasses;?>" type="search" name="search" id="<?=$id;?>" placeholder="<?=$placeholder;?>" autocomplete="off">

    <div class="search-select__dropdown search-dropdown disable">
    </div>

    <div class="search-select__values">
        <?php foreach($items as $id => $item):?>
            <input type="hidden" name="search-select-item[<?=$id;?>]" id="search-select-item-<?=$id;?>" value="<?=$item;?>">
        <?php endforeach;?>
    </div>
</div>

