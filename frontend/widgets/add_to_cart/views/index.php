<?php
/**@var int $id*/
/**@var int $cartId*/
/**@var bool $status*/
/**@var int $quantity*/
/**@var bool $isAvailable*/
/**@var int $maxQuantity*/
/**@var string $classes*/
/**@var string $title*/
/**@var string $brand*/
/**@var string $oldPrice*/
/**@var string $price*/
/**@var string $genres*/
/**@var string $list_id*/
/**@var string $list_name*/
?>
<div class="add-to-cart-wrapper <?=$classes;?>"
     data-product-id="<?= $id ?>"
     data-product-name="<?= $title ?>"
     data-product-brand="<?= $brand ?>"
     data-product-oldPrice="<?= $oldPrice ?>"
     data-product-price="<?= $price ?>"
     data-product-genres="<?= $genres ?>"
     data-product-list_id="<?= $list_id ?>"
     data-product-list_name="<?= $list_name ?>"
>
    <div class="mini-card__button mini-card__button-count button-tocart counter <?=$status ? 'counter_active' : 'counter_hidden';?>">
        <button  id="minus-<?=$id;?>" class="mini-card__button-count_minus counter__button counter__button_minus" data-product="<?=$cartId;?>"></button>
        <div class="mini-card__button-count-input counter__input">
            <input id="counter-<?=$id;?>" type="text" disabled value="<?= $quantity > 0 ? $quantity : 1?>" data-range="<?=$maxQuantity;?>">
        </div>
        <button  id="plus-<?=$id;?>" class="mini-card__button-count_plus counter__button counter__button_plus <?= $maxQuantity === 1 ? 'disable' : '';?>" data-product="<?=$cartId;?>"></button>
    </div>

    <button class="mini-card__button mini-card__button-tocart button-tocart  <?=$status ? 'hidden' : 'active';?>" data-product="<?=$id;?>">В корзину</button>
</div>
