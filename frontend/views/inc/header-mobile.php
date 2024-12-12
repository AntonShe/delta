<div class="header-mobile js-header-mobile">
    <ul class="header-mobile__list flex">
        <li class="header-mobile__item">
            <button id="mobile-catalog" class="header-mobile__button header-mobile__button-catalog"></button>
        </li>
        <li class="header-mobile__item header__cart">
            <a href="/cart" class="header-mobile__item-link">
                <div class="header__icon-counter hidden js-cart-count">0</div>
            </a>
        </li>
        <li class="header-mobile__item header__favorite">
            <a href="/profile/favourite" class="header-mobile__item-link">
                <div class="header__icon-counter hidden js-favorite-count">0</div>
            </a>
        </li>
        <li class="header-mobile__item">
            <div class="otherPages b-nav-tab" data-modal="modalOne">
                <?php if ($this->context->getIsGuest()): ?>
                    <button id="mobile-login" class="header-mobile__button header-mobile__button-login js-login-button"></button>
                <?php else: ?>
                    <a href="/profile">
                        <button id="mobile-profile" class="header-mobile__button header-mobile__button-login"></button>
                    </a>
                <?php endif;?>
            </div>
        </li>
    </ul>
</div>