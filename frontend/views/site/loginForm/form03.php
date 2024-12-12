<?php
/** @var array $params */
/** @var array $activeTab */
?>
<!-- start Вход по паролю  -->
<form action="#" class="popup__inputs-wrapper js-form-left b-tab <?=$activeTab == 1 ? 'active' : 'disabled';?>" id="signin" data-role="regular">
    <div class="popup__tabs-input">
        <input class="popup__input-email input" type="email" name="email" id="popup-email-log" placeholder="Почта*" required="true">
        <div class="popup__password-wrap">
            <input class="popup__input-password input" type="password" name="password" id="popup-password" placeholder="Пароль*" required="true">
            <button type="button" class="popup__password-button popup__password-button_off"></button>
        </div>
    </div>
    <p class="error-message"></p>
    <div class="popup__buttons flex">
        <button type="button" data-tab="restorePass" class="popup__button button-red button-restore popup__tab b-nav-tab">Восстановить пароль</button>
        <button type="submit" class="popup__button button-black js-password-btn">Войти</button>
    </div>
</form>
<!-- end Вход по паролю  -->