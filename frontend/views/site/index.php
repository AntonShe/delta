<?php

/** @var yii\web\View $this */
use frontend\widgets\banners\BannersWidget;
use frontend\widgets\courses_on_main\CoursesOnMainWidget;
use frontend\widgets\genres_on_main\GenresOnMainWidget;
use frontend\widgets\shelves\ShelvesWidget;

?>
<section class="section promotion container">

    <?= BannersWidget::widget() ?>

    <?= GenresOnMainWidget::widget() ?>
</section>

<?= ShelvesWidget::widget() ?>

<!-- <section class="section container big-banner">
    <a href="/catalog/1746?erid=2VtzqxTdiQ2">
        <div class="big-banner__desktop"><img src="/img/big-banner/course/course-banner-desktop2.png" alt="Популярный курс английского языка для средней школы"></div>
        <div class="big-banner__table"><img src="/img/big-banner/course/course-banner-table2.png" alt="Популярный курс английского языка для средней школы"></div>
        <div class="big-banner__mobile"><img src="/img/big-banner/course/course-banner-mobile2.png" alt="Популярный курс английского языка для средней школы"></div>
    </a>
</section> -->

<section class="section container big-banner">
    <div class="big-banner__desktop"><img src="/img/big-banner/books/books-banner-desktop.png" alt="Учебники и книги на иностранных языках"></div>
    <div class="big-banner__table"><img src="/img/big-banner/books/books-banner-table.png" alt="Учебники и книги на иностранных языках"></div>
    <div class="big-banner__mobile"><img src="/img/big-banner/books/books-banner-mobile.png" alt="Учебники и книги на иностранных языках"></div>
</section>

