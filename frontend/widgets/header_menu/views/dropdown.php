<?php
/** @var array $list */
/** @var array $genresList */
?>
<div class="header__dropdown">
    <div class="dropdown" id="catalog-dropdown">
        <div class="header__dropdown-container">
            <div class="header__dropdown-link dropdown__city js-user-location"></div>
            <?php foreach ($list as $name => $link): ?>
                <?php if($link): ?>
                    <a href="<?= $link ?>" class="header__dropdown-link dropdown__item button-text"><?= $name ?></a>
                <?php endif; ?>
            <?php endforeach; ?>
            <ul class="dropdown__list dropdown__list_first">
                <?php foreach ($genresList as [
                    'name' => $name,
                    'link' => $link,
                    'children' => $children,
                    'isEnd' => $isEnd
                ]): ?>
                    <li class="dropdown__item dropdown__item_first<?= $isEnd ? ' dropdown__item_font' : '' ?>">
                    <div class="dropdown__item-link">
                        <?= $name ?>
                    </div>
                    <?php if(!empty($children)): ?>
                        <ul class="submenu dropdown__list">
                            <button class="dropdown__item-back">
                                Назад
                            </button>
                            <?php foreach ($children as ['name' => $name, 'link' => $link]): ?>
                                <li class="dropdown__item">
                                    <a href="<?= $link ?>" class="dropdown__item-link">
                                        <?= $name ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>