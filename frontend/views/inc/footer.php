<?php

use frontend\widgets\footer_menu\FooterMenuWidget;

?>
<footer class="footer">
    <div class="footer__container">
        <div class="footer__top">
            <div class="footer__contacts-tel footer__top-item">
                <div class="footer__contacts-tel-element">
                    <a class="footer__contacts-tel-link" href="tel:+74995527176">8 (499) 552-71-76</a>
                    <p class="footer__contacts-tel-text">Отдел продаж</p>
                </div>
                <div class="footer__contacts-tel-element">
                    <a class="footer__contacts-tel-link" href="tel:+78007001006">8 (800) 700-10-06 </a>
                    <p class="footer__contacts-tel-text">Доставка</p>
                </div>
            </div>
            <?= FooterMenuWidget::widget() ?>
            <div class="footer__contacts-top footer__top-item">
                <div class="footer__contacts">
                    <a class="footer__contacts-link" href="mailto:shop@deltabook.ru">shop@deltabook.ru</a>
                    <div class="footer__contacts-container">
                        <a rel="nofollow" href="https://t.me/deltabookru" class="footer__contact-link"><span class="footer__contact footer__contact-telegram"></span>Телеграм</a>
                        <a rel="nofollow" href="https://vk.com/deltabook" class="footer__contact-link"><span class="footer__contact footer__contact-vk"></span>Вконтакте</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer__line"></div>
        <div class="footer__bottom">
            <div class="footer__logo-container">
                <a href="#" class="footer__logo">
                    <img src="/img/logo-footer.svg" alt="">
                </a>
                <div class="footer__logo-descr">
                    © Deltabook.ru, 2008-<?= date('Y') ?>
                    <br />
                    Все права защищены
                </div>
            </div>
            <div class="footer__contacts-bottom">
                <div class="footer__contacts">
                    <a class="footer__contacts-link" href="mailto:shop@deltabook.ru">shop@deltabook.ru</a>
                    <div class="footer__contacts-container">
                        <a rel="nofollow" href="https://t.me/deltabookru" class="footer__contact-link"><span class="footer__contact footer__contact-telegram"></span>Телеграм</a>
                        <a rel="nofollow" href="https://vk.com/deltabook" class="footer__contact-link"><span class="footer__contact footer__contact-vk"></span>Вконтакте</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>