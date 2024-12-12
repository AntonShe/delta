<?php
/** @var int $id */
/** @var bool $status */
/** @var string $classes */
?>
<div class="<?=$classes;?> button-like">
    <button class="button-like__btn js-button-like <?=$status ? 'button-like-active' : '';?>" data-product="<?=$id;?>"></button>
    <div class="button-like__tooltip js-button-like-tooltip"><?=$status ? 'Убрать из избранного' : 'В избранное';?></div>
</div>