<?php
/** @var array $data */
/** @var array $products */
?>
<section class="section course-block container flex">
    <div class="course-block__left">
        <p class="course-block__name-course">
            <?= $data->name ?>
        </p>
        <h2 class="course-block__title">
            <?= $data->onMainInfo['title'] ?>
        </h2>
        <p class="course-block__descr">
            <?= $data->onMainInfo['subtitle'] ?>
        </p>
        <div class="course-block__wrapper flex">
            <span class="course-block__descr_color">
                <?= $data->onMainInfo['text'] ?>
            </span>
            <a href="/catalog/<?= $data->id ?>" class="course-block__btn button-black-big">Смотреть курс</a>
        </div>
    </div>
    <div class="course-block__right">
        <div class="banner">
            <?php foreach($products as $key => $product): ?>
                <img class="banner__img-<?= $key + 1 ?>" src="<?= $product->cover ?: '/img/empty.png' ?>" alt="<?= $product->title ?>">
            <?php endforeach; ?>
        </div>
    </div>
</section>
