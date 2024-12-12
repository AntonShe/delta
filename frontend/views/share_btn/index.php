<?php
/** @var bool $link */
?>

<div class="share-btn__main-item share-btn__main-share flex">
    <a target="_blank" id="tg" class="share-btn__share-btn share-btn__share-btn-tg js-share-btn"></a>
    <a target="_blank" id="vk" class="share-btn__share-btn share-btn__share-btn-vk js-share-btn"></a>
    <a target="_blank" id="ok" class="share-btn__share-btn share-btn__share-btn-ok js-share-btn"></a>

    <?php if ($link): ?>
    <button class="share-btn__share-btn-link js-copy-share-link">
        <span class="tooltip share-btn__tooltip">Скопировать ссылку</span>
    </button>
    <?php endif; ?>
</div>
