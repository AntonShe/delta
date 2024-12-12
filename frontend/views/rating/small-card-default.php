<?php
/** @var bool $isMobile */
/** @var float $value */
?>
<?php if($value): ?>
    <div class="get-rating">
        <?php for($i = 1; $i <= 5; $i++): ?>
            <div class="star<?= round($value) >= $i ? ' active' : '' ?>"></div>
        <?php endfor; ?>
    </div>
    <div class="mini-card__number"><?= $value ?></div>
<?php endif; ?>
