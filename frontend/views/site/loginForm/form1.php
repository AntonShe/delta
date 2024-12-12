<?php
/** @var array $email */
/** @var array $phone */
?>

<!-- SMS Код на телефон - Восстановить пароль. Код на почту-->

<div id="modalTwo" class="popup modal js-popup-modal">
    <div class="popup__overlay"></div>
    <div class="popup__container js-popup-container">
        <div class="popup__header-mobile">
            <div class="popup__header-logo">
                <img src="/img/logo-header.svg" alt="">
            </div>
            <div class="popup__header-mobile-wrapper">
                <button class="popup__header-button button-back js-popup-button-back"></button>
                <div class="popup__header-title">Вход и регистрация</div>
            </div>
        </div>        
        <div class="popup__header flex popup__header--desk">
            <button type="button" class="popup__header-button button-back js-popup-button-back"></button>
            <div class="popup__header-title">Вход и регистрация</div>
        </div>
        <div class="popup__inputs-wrapper">
            <form action="#" id="verify-code">
                <div class="popup__tabs-input">
                    <input class="popup__input-code input" type="number" name="code[1]" id="popup-code-1" required>
                    <input class="popup__input-code input" type="number" name="code[2]" id="popup-code-2" required>
                    <input class="popup__input-code input" type="number" name="code[3]" id="popup-code-3" required>
                    <input class="popup__input-code input" type="number" name="code[4]" id="popup-code-4" required>
                    <input class="popup__input-code input" type="number" name="code[5]" id="popup-code-5" required>
                </div>
                <p class="error-message"></p>
                <?php if(!empty($phone)):?>
                    <div class="popup__inputs-text">
                        На номер <span><?=$phone?></span> отправили SMS-код. Введите его
                    </div>
                <?php else:?>
                    <div class="popup__inputs-text">
                        На почту <a href="#"><?=$email?></a> отправлено письмо с кодом. Введите его
                    </div>
                <?php endif;?>
                <div class="popup__buttons flex">
                    <button type="button" class="popup__button button-black button-disable center js-resender-btn" data-timer="5" id="resender">
                        Повторить отправку <span></span>
                    </button>
                    <button type="submit" class="hidden" id="verify-pin">Продолжить</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- SMS Код на телефон - Восстановить пароль. Код на почту-->