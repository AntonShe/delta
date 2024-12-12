<?php
/** @var string $otherContent */
/** @var string $activeTab */
/** @var string $title */
/** @var string $buttonBack */
?>
<div id="modalOne" class="popup modal js-popup-modal">
    <div class="popup__overlay"></div>
    <div class="popup__container js-popup-container">
        <div class="flex popup__header popup__header--desk">
            <?php if ($buttonBack): ?>
                <button class="popup__header-button button-back js-popup-button-back"></button>
            <?php endif; ?>

            <div class="popup__header-title"><?= $title ? "$title" : 'Войти или зарегистрироваться';?></div>
        </div>
        <div class="popup__header-mobile">
            <div class="popup__header-logo">
                <img src="/img/logo-header.svg" alt="">
            </div>
            <div class="popup__header-mobile-wrapper">
                <button class="popup__header-button button-back <?= $buttonBack ? 'js-popup-button-back' : 'js-popup-button-close';?>"></button>
                <div class="popup__header-title"><?= $title ? "$title" : 'Войти или зарегистрироваться';?></div>                   
            </div>
        </div>

        <?=$otherContent;?>

    </div>
</div>
