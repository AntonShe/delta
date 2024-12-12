<?php
    use frontend\widgets\header_menu\HeaderMenuWidget;
    use frontend\widgets\search\SearchWidget;
?>
<header class="header">
    <div class="header__top">
        <div class="header__top-container container flex">
            <div class="header__location js-user-location"> </div>
            <div class="header__contacts flex">
                <div class="header__contacts-mail">
                    <a class="header__contacts-link" href="mailto:shop@deltabook.ru">shop@deltabook.ru</a>                    
                </div>
                <div>
                    <a class="header__contacts-link" href="tel:+74995527176">8 (499) 552-71-76</a>
                    <p class="header__contacts-item">Отдел продаж</p>
                </div>
                <div>
                    <a class="header__contacts-link" href="tel:+78007001006">8 (800) 700-10-06 </a>
                    <p class="header__contacts-item">Доставка</p>
                </div>
            </div>
        </div>
    </div>
    <?php $menu = HeaderMenuWidget::begin(); ?>
    <div class="header__bottom">
        <div class="header__bottom-container container flex">
            <div class="header__bottom-left flex">
                <a href="/" class="header__left-item header__bottom-logo">
                    <img src="/img/logo-header.svg" alt="">
                </a>
                <?= $menu->renderList() ?>
                <?= SearchWidget::widget(); ?>
            </div>
            <div class="header__bottom-right flex">
                <a href="/cart" class="header__icon-wrapper">
                    <span class="header__cart">
                        <div class="header__icon-counter hidden js-cart-count">0</div>
                    </span>
                    <span class="header__icon-text">Корзина</span>                    
                </a>
                <a href="/profile/favourite" class="header__icon-wrapper">
                    <span class="header__favorite">
                        <div class="header__icon-counter hidden">0</div>
                    </span>
                    <span class="header__icon-text">Избранное</span>                   
                </a>
                <div class="b-nav-tab user" data-modal="modalOne">
                    <?php if ($this->context->getIsGuest()): ?>
                    <div class="header__icon-wrapper js-login-button">    
                        <div class="header__account"></div>
                        <span class="header__icon-text">Войти</span>
                    </div>
                    <?php else: ?>
                    <a href="/profile" class="header__icon-wrapper">
                        <span class="header__account"></span>
                        <span class="header__icon-text">Профиль</span>                        
                    </a> 
                    <?php endif;?>
                </div>
            </div>
        </div>
    </div>

    <?= $menu->renderDropdown() ?>
    <?php HeaderMenuWidget::end(); ?>
</header>


