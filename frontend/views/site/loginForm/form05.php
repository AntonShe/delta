<?php
/** @var array $params */
?>
<!-- start Новый пароль  -->
<div class="popup__inputs-wrapper b-tab" id="newPass">
    <a data-tab="signinPass" class="b-nav-tab">
        <div class="popup__header flex">
            <button class="popup__header-button button-back js-popup-button-back"></button>
            <div class="popup__header-title">Восстановить пароль</div>
        </div>
    </a>
    <form action="#">
        <div class="popup__tabs-input">
            <div class="popup__password-wrap">
                <input class="popup__input-password input" type="password" name="password" id="popup-password" placeholder="Новый пароль*" required="true">
                <button type="button" class="popup__password-button popup__password-button_off"></button>
            </div>
            <div class="popup__password-wrap">
                <input class="popup__input-password input" type="password" name="password" id="popup-password" placeholder="Повторите пароль*" required="true">
                <button type="button" class="popup__password-button popup__password-button_off"></button>
            </div>
        </div>
        <div class="popup__text-verification">aliona.golovanova89@gmailcom </div>
        <div class="popup__buttons flex">
            <button type="submit" class="popup__button button-black button-black_size">Изменить пароль</button>
        </div>
    </form>
</div>
<!-- end Новый пароль  -->