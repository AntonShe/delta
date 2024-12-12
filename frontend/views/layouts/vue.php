<?php

/** @var \yii\web\View $this */
/** @var string $content */

use frontend\assets\VueAppAsset;
use frontend\widgets\meta_tags\MetaTagsWidget;
use yii\bootstrap5\Html;

VueAppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="ru_RU" class="h-100">
    <head>
        <?= MetaTagsWidget::widget(['isVue' => true]); ?>

        <?php $this->registerCsrfMetaTags() ?>
        <?php $this->head() ?>
        <!-- Yandex.Metrika counter -->
        <script type="text/javascript" >
            (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
                m[i].l=1*new Date();
                for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}
                k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
            (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

            ym(94259497, "init", {
                clickmap:true,
                trackLinks:true,
                accurateTrackBounce:true,
                webvisor:true,
                ecommerce:"dataLayer"
            });
        </script>
        <noscript><div><img src="https://mc.yandex.ru/watch/94259497" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
        <!-- /Yandex.Metrika counter -->
        <script>
            window.dataLayer = window.dataLayer || [];
            const userId = "<?= Yii::$app->user->getId() ?>";
            if (userId) {
                window.dataLayer.push({
                    'user_id': userId
                });
            }
        </script>
        <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-MHV3G5VW');</script>
        <!-- End Google Tag Manager -->
    </head>
    <body class="d-flex flex-column h-100">

    <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MHV3G5VW" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

        <?php $this->beginBody() ?>

        <?= $this->render('@app/views/inc/header') ?>

        <div class="popup-location-container js-location-container container"></div>

        <main class="main">
            <div id="main" class="main__wrapper"></div>

            <?= $this->render('@app/views/inc/header-mobile') ?>
        </main>

        <div class="popup-container"></div>

        <?= $this->render('@app/views/inc/footer') ?>

        <?php $this->endBody() ?>
        <script
            src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"
            integrity="sha256-lSjKY0/srUM9BE3dPm+c4fBo1dky2v27Gdjm2uoZaL0="
            crossorigin="anonymous"></script>
    </body>
    </html>
<?php $this->endPage();