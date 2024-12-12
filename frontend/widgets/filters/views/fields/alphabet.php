<?php
/** @var string $title */
/** @var array $list */
/** @var string $template */
/** @var array $value */
/** @var array $paramName */
?>
<div class="filter__title"><?= $title ?></div>
<div class="filter__alphabet js-filters-checkboxes" data-name="<?= $paramName ?>">
    <?php foreach ($list as $item): ?>
        <input class="filter__alphabet-input js-filters-checkboxes-input"
               type="checkbox"
               value="<?= $item ?>"
               id="<?= $paramName ?>-<?= $item ?>"
        >
        <label class="filter__alphabet-label" for="<?= $paramName ?>-<?= $item ?>">
            <?= $item ?>
        </label>
    <?php endforeach; ?>
</div>