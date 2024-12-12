<?php
/** @var array $list */
?>
<div class="card-catalog__container">
    <h2 class="card-catalog__title">Еще книги из серии</h2>

    <div class="card-catalog__wrapper grid">
        <?php foreach ($list as $key=>$item): ?>
            <?= $this->render('@app/views/product_cards/default', ['data' => $item, 'index' => $key, 'list_id' => "series", 'list_name' => "Еще книги из серии"]) ?>
        <?php endforeach; ?>
    </div>
</div>