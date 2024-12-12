<?php
/** @var AbstractDecorator $data */

use common\models\AbstractDecorator;
?>
<a href="<?= $data->url ?>" class="link course-card flex">
    <div class="course-card__left">
        <img src="/img/empty.png" alt="<?= $data->name ?>">
    </div>
    <div class="course-card__right">
        <?= $data->name ?>
    </div>
</a>