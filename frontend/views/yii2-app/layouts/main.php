<?php

use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $content string */

if (Yii::$app->controller->action->id === 'login' || Yii::$app->controller->action->id === 'error') {
    /**
     * Do not use this code in your template. Remove it.
     * Instead, use the code  $this->layout = '//main-login'; in your controller.
     */
    echo $this->render(
        'main-login',
        ['content' => $content]
    );
} else {

    if (class_exists('frontend\assets\AppAsset')) {
        frontend\assets\AppAsset::register($this);
    }

    dmstr\web\AdminLteAsset::register($this);
    dmstr\widgets\Menu::$iconClassPrefix = '';
    ini_set('memory_limit', '-1');
    $directoryAsset = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');
    require Yii::$app->basePath . '/controllers/SidebarController.php'; // заменить на метод для корректной работы тестов. было require_once
    ?>
    <?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <!-- <meta name="viewport" content="width=device-width, initial-scale=1"> -->
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body class="hold-transition skin-blue sidebar-mini sidebar-collapse">
    <?php $this->beginBody() ?>
    <div class="wrapper">

        <?= $this->render(
            'header.php',
            [
                'directoryAsset' => $directoryAsset
            ]
        ) ?>

        <?php
        echo $this->render('left.php', ['directoryAsset' => $directoryAsset]);
        ?>
        <?= $this->render(
            'content.php',
            [
                'content' => $content,
                'directoryAsset' => $directoryAsset
            ]
        ) ?>

        <?= $this->render(
            'control.php',
            [
                'content' => $content,
                'directoryAsset' => $directoryAsset,
            ]
        ) ?>

    </div>
    <?php $this->endBody() ?>
    </body>
    </html>
    <?php $this->endPage() ?>
<?php } ?>
