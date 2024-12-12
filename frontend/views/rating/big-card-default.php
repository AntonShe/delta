<?php
/** @var float $value */
/** @var string $text */

$class = 'medium';

if ($value >= 4) {
    $class = 'high';
} elseif ($value <= 2) {
    $class = 'low';
}

?>
<div class="big-card__top-rating <?= $class ?>"><?= $value ?></div>
<div class="big-card__top-rating-sum"><?= $text ?></div>