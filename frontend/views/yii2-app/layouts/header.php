<?php

use common\models\User;
use yii\helpers\Html;
use yii\web\View;

/* @var $style */
/* @var $this View */
/* @var $currentUser /console/model/Users */
/* @var $events_all */
/* @var $content string */


$currentUser = Yii::$app->view->params['currentUser'];

$events = Yii::$app->view->params['events'];

$register_unread = Yii::$app->view->params['register_unread'];

$deleted = Yii::$app->view->params['deleted'];
$userImage = Yii::$app->view->params['userImage'];
$userImage = $currentUser->getImageUrl();
?>

<header class="main-header">

    <?= Html::a('<span class="logo-mini">П</span><span class="logo-lg">' . Yii::$app->name = '' . Yii::t('app', 'ПолиТЭР') . '</span>',
        Yii::$app->homeUrl, ['class' => 'logo']) ?>
    <nav class="navbar navbar-static-top">
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button" style="padding: 15px 15px">
            <span class="sr-only"><?php echo Yii::t('app', 'Навигация') ?></span>
        </a>
        <div class="navbar-custom-menu" style="padding-top: 0; padding-bottom: 0">
            <ul class="nav navbar-nav">
                <?php
                if (Yii::$app->user->can(User::PERMISSION_ADMIN) || Yii::$app->user->can(User::PERMISSION_OPERATOR))
                    echo $this->render('header_timeline');
                if (Yii::$app->user->can(User::PERMISSION_ADMIN))
                    echo $this->render('header_register', ['register_unread' => $register_unread]);
                ?>
                <?= $this->render('header_events', ['events' => $events]); ?>
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <?php
                        echo '<img src="' . $userImage . '" class="user-image" alt="U">';
                        ?>
                        <span class="hidden-xs">
                            <?php
                            if ($currentUser) echo $currentUser['name'];
                            ?>
                        </span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- User image -->
                        <li class="user-header">
                            <?php
                            echo '<img src="' . $userImage . '" class="img-circle" alt="U">';
                            ?>
                            <p>
                                <?php
                                if ($currentUser) echo $currentUser['username'];
                                if ($currentUser) echo '<small>' . $currentUser['name'] . '</small>';

                                ?>
                            </p>
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-left">
                                <?= Html::a(Yii::t('app', 'Профиль'), ['user/view', 'id' => $currentUser['id']],
                                    ['class' => 'btn btn-default btn-flat']) ?>
                            </div>
                            <div class="pull-right">
                                <?= $menuItems[] = Html::beginForm(['/logout'], 'post')
                                    . Html::submitButton(
                                        Yii::t('app', 'Выйти'),
                                        [
                                            'class' => 'btn btn-default btn-flat',
                                            'style' => 'padding: 6px 16px 6px 16px;'
                                        ]
                                    )
                                    . Html::endForm();
                                ?>
                            </div>
                        </li>
                    </ul>
                </li>
                <?php
                echo $this->render('header_trash', ['deleted' => $deleted]);
                if (Yii::$app->user->can(User::PERMISSION_ADMIN))
                    echo $this->render('header_control');
                ?>
            </ul>
        </div>
    </nav>

</header>
