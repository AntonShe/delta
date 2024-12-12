<?php
/** @var array $email */
/** @var array $type */
/** @var string $activeTab */
?>
<!-- start Регистрация. пароль  -->
<form action="#" class="popup__inputs-wrapper <?=!empty($type) && $type == 'regular' ? 'js-form-left' : 'js-form-right';?> b-tab active" id="<?=!empty($type) && $type == 'regular' ? 'signin' : 'registration';?>" data-type="submit-pass">
    <div class="popup__tabs-input">
        <div class="popup__password-wrap">
            <input class="popup__input-password input" type="password" name="password" id="password" placeholder="<?=!empty($type) && $type == 'regular' ? 'Новый пароль*' : 'Пароль*';?>" required="true">
            <button type="button" class="popup__password-button popup__password-button_off"></button>
        </div>
        <div class="popup__password-wrap">
            <input class="popup__input-password input" type="password" name="consume-password" id="consume-password" placeholder="Повторите пароль*" required="true">
            <button type="button" class="popup__password-button popup__password-button_off"></button>
        </div>
    </div>
    <p class="error-message"></p>
    <div class="popup__text-verification"><?=$email;?></div>
    <div class="popup__buttons flex">
        <button type="submit" class="popup__button button-black button-black_size"><?=!empty($type) && $type == 'regular' ? 'Изменить пароль' : 'Зарегистрироваться';?></button>
    </div>
</form>
<!-- end Регистрация. пароль  -->