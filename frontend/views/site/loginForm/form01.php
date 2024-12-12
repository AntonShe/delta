<?php
/** @var string $activeTab */
?>
<!-- start Вход по телефону  -->
<form action="#" class="popup__inputs-wrapper js-form-left b-tab <?=$activeTab == 1 ? 'active' : 'disabled';?>" id="signin" data-role="same">
    <div class="popup__tabs-input">
        <input class="popup__input-tel input" type="phone" name="phone" id="popup-phone" value="+7" required>
    </div>
    <p class="error-message"></p>
    <div class="popup__inputs-text">
        Отправим на&nbsp;ваш номер SMS-сообщение с&nbsp;кодом подтверждения
    </div>
    <div class="popup__buttons flex">
        <button data-tab="signinPass" class="popup__button button-red login-pass popup__tab b-nav-tab">Войти по паролю</button>
        <div class="otherPages" data-modal="modalTwo">
            <button type="submit" class="popup__button button-black get-code js-get-code-btn">Получить код</button>
        </div>
    </div>
    <div class="popup__info">
        Нажимая кнопку &laquo;Получить код&raquo;, я&nbsp;подтверждаю, что достиг(ла) 18&nbsp;лет, ознакомлен(а) с&nbsp;<a class="popup__link" href='/user-agreement' target="_blank">Пользовательским соглашением</a> и&nbsp;согласен(а) на&nbsp;получение информационных email и&nbsp;СМС рассылок от&nbsp;ООО &laquo;Цунами Букс&raquo; (ИНН 9725093665)
    </div>
</form>
<!-- end Вход по телефону  -->