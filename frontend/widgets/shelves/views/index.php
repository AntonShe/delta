<?php
/** @var array $list */
?>

<?php foreach($list as $item): ?>
    <section class="shelf-cards container js-shelf-cards"
             data-productList-id="shelf_<?= $item['id'] ?>"
             data-productList-name="<?= $item['name'] ?>"
>
        <div class="shelf-cards__wrapper">
            <h2 class="shelf-cards__title">
                <?php if($item['urlName']): ?>
                    <a class="shelf-cards__link" href="<?= $item['urlName'] ?>"><?= $item['name'] ?></a>
                <?php else: ?>
                    <?= $item['name'] ?>
                <?php endif; ?>
            </h2>

            <div class="shelf-cards__swiper swiper">

                <div class="shelf-cards__swiper-button button-swiper button-swiper-prev"></div>
                <div class="shelf-cards__swiper-button button-swiper button-swiper-next"></div>

                <div class="shelf-cards__swiper-wrapper swiper-wrapper">
                    <?php if(!empty($item['products'])): ?>
                        <?php foreach ($item['products'] as $key=>$data): ?>
                            <div class="shelf-cards__swiper-slide swiper-slide">
                                <?= $this->render('@app/views/product_cards/default', ['data' => $data, 'index' => $key, 'list_id' => "shelf_" . $item['id'], 'list_name' => $item['name']]) ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
<?php endforeach; ?>