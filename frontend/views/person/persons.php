<?php
/** @var array $persons */
/** @var bool $isBigCard */


if ($isBigCard) {
    foreach ($persons as &$person) {
        $person['url'] = "<a class='big-card__person-link' href='/person/{$person['id']}'>{$person['name']}</a>";
    }
} else {
    foreach ($persons as &$person) {
        $person['url'] = "<a class='mini-card__person-link' href='/person/{$person['id']}'>{$person['name']}</a>";
    }
}
?>
<?= implode(', ', array_column($persons, 'url'))?>

