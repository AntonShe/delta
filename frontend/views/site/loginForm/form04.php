<?php
/** @var array $activeTab */
?>
<!-- start Восстановить пароль  -->
<form action="#" class="popup__inputs-wrapper js-form-left b-tab <?=$activeTab == 1 ? 'active' : 'disabled';?>" id="restore">
    <div class="popup__tabs-input">
        <input class="popup__input-email input" type="email" name="email" id="popup-email-restore" placeholder="Почта"
               required="true">
    </div>
    <div class="popup__info">
        Мы отправим на вашу почту письмо с кодом для восстановления пароля
    </div>
    <div class="popup__buttons flex">
        <button type="submit" class="popup__button button-black get-code">Получить код</button>
    </div>
</form>
<!-- end Восстановить пароль  -->