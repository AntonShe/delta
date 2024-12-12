<?php
/** @var string $activeTab */
?>
<!-- start Регистрация основная  -->
<form action="#" class="popup__inputs-wrapper js-form-left b-tab <?=$activeTab == 1 ? 'active' : 'disabled';?>" id="restore">
    <div class="popup__tabs-input">
        <input class="popup__input-email input" type="email" name="email" id="popup-email-restore" placeholder="Почта*" required>
    </div>
    <p class="error-message"></p>
    <div class="popup__buttons flex">
        <div class="otherPages" data-modal="modalTwo">
            <button type="submit" class="popup__button button-black button-black_size get-code">Получить код</button>
        </div>
    </div>
    <div class="popup__info">
        Нажимая кнопку &laquo;Получить код&raquo;, я&nbsp;подтверждаю, что достиг(ла) 18&nbsp;лет, ознакомлен(а) с&nbsp;<a class="popup__link" href='/user-agreement' target="_blank">Пользовательским соглашением</a> и&nbsp;согласен(а) на&nbsp;получение информационных email и&nbsp;СМС рассылок от&nbsp;ООО &laquo;Цунами Букс&raquo; (ИНН 9725093665)
    </div>
</form>
<!-- end Регистрация основная  -->