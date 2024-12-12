<?php
/** @var string $title */
/** @var string $description */
/** @var string $keywords */
/** @var string $canonical */
/** @var bool $noindex */

use yii\helpers\Html;

?>
<meta charset="<?= Yii::$app->charset ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta property="og:title" content="<?= $title ?>">
<meta property="og:type" content="company">
<meta property="og:url" content="<?= $canonical ?>">
<meta property="og:site_name" content="deltabook.ru">
<meta name="description" content="<?= $description ?>">
<meta name="keywords" content="<?= $keywords ?>">

<title><?= Html::encode($title) ?></title>

<link rel="canonical" href="<?= $canonical ?>"/>
<link rel="shortcut icon" href="/favicon.ico">

<?php if($noindex): ?>
    <meta name="robots" content="NOINDEX, FOLLOW">
<?php endif; ?>
