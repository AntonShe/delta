<?php
/** @var string $title */
/** @var array $list */
/** @var string $template */
/** @var array $value */
/** @var array $paramName */
?>
<?php if(!empty($list)): ?>
    <div class="filter js-filters-checkboxes" data-name="<?= $paramName ?>">
    <div class="filter__title">
        <?= $title ?>
    </div>
    <?php if(count($list) > 6): ?>
    <div class="js-container">
        <div class="filter__input-wrap js-show-more-content">
    <?php endif; ?>
    <?php foreach ($list as $item): ?>
        <div class="filter__input-block">
            <input class="filter__checkbox-input js-filters-checkboxes-input"
               type="checkbox"
               value="<?= $item->id ?>"
               id="<?= $paramName ?>-<?= $item->id ?>"
            >
            <label class="filter__checkbox-label" for="<?= $paramName ?>-<?= $item->id ?>">
                <span class="filter__checkbox-text" data-tooltip-label="<?= $item->name ?>"><?= $item->name ?></span>
            </label>
        </div>
    <?php endforeach; ?>

    <?php if(count($list) > 6): ?>
        </div>
        <div class="filter__btn button-text button-more js-show-more-button" data-text="Еще">
            Еще
        </div>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>
