<?php
/** @var array $list */
?>
<ul class="breadcrumb flex" itemscope itemtype="http://schema.org/BreadcrumbList">
    <?php $pos = 1; ?>
    <?php foreach($list as ['title' => $title, 'link' => $link, 'isActive' => $isActive]): ?>
        <li class="breadcrumb__item<?= $isActive ? ' breadcrumb__item_active' : ''?>" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
            <?php if ($link): ?><a href="<?= $link ?>" class="breadcrumb__item-link" itemprop="item"><?php endif; ?>
                <span itemprop="name<?= $link ? '' : ' item'; ?>"><?= $title ?></span>
            <?php if ($link): ?></a><?php endif; ?>
            <meta itemprop="position" content="<?= $pos ?>" />    
        </li>
        <?php $pos++; ?>
    <?php endforeach; ?>
</ul>
