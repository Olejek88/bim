<?php

use common\models\User;
use common\models\Users;
use Da\QrCode\QrCode;
use frontend\models\Role;
use yii\helpers\Html;

/* @var $model User */
/* @var $user_property */
/* @var $events */
/* @var $tree */
/* @var $role Role */
/* @var $roleList array */

$this->title = Yii::t('app', 'Профиль пользователя') . " " . $model->username;
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <?php echo Yii::t('app', 'Профиль пользователя') ?>
        </h1>
        <ol class="breadcrumb">
            <li><?php echo Html::a(Yii::t('app', 'Главная'), '/') ?></li>
            <li><?php echo Html::a(Yii::t('app', 'Пользователи'), '/users/dashboard') ?></li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-3">
                <!-- Profile Image -->
                <div class="box box-primary">
                    <div class="box-body box-profile">
                        <?php
                        $path = $model->getImageUrl();
                        //$path = str_replace("storage","files", $path);
                        if (!$path || !$model['image']) {
                            $path = Yii::$app->request->baseUrl . '/images/unknown2.png';
                        }
                        echo '<img class="profile-user-img img-responsive img-circle" src="' . Html::encode($path) . '">';
                        ?>
                        <h3 class="profile-username text-center"><?php echo $model['username'] ?></h3>
                        <p class="text-muted text-center"><?php echo $model['name'] ?></p>
                    </div>
                </div>

                <!-- About Me Box -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo Yii::t('app', 'Информация') ?></h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <strong><i class="fa fa-check-circle margin-r-5"></i><?php echo Yii::t('app', 'Статус') ?>
                        </strong>
                        <?php
                        if ($model['status'] == 10) echo '<span class="label label-success">' . Yii::t('app', 'Активен') . '</span>';
                        else echo '<span class="label label-danger">' . Yii::t('app', 'Не активен') . '</span>';
                        ?>
                        <br/>
                        <br/>
                        <strong><i class="fa fa-users margin-r-5"></i><?php echo Yii::t('app', 'Роль') ?>
                        </strong>
                        <?php
                        $assignments = Yii::$app->getAuthManager()->getAssignments($model['id']);
                        foreach ($assignments as $value) {
                            if ($value->roleName == User::ROLE_ADMIN)
                                echo '<span class="label label-danger">' . Yii::t('app', 'Администратор') . '</span>';
                            if ($value->roleName == User::ROLE_OPERATOR)
                                echo '<span class="label label-success">' . Yii::t('app', 'Оператор') . '</span>';
                            break;
                        }
                        ?>
                    </div>
                </div>
            </div><!---->
            <!-- /.col -->
            <div class="col-md-9">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active" style="margin-right: 0"><a href="#timeline" data-toggle="tab">
                                <?php echo Yii::t('app', 'Журнал') ?></a>
                        </li>
                        <li style="margin-right: 0"><a href="#settings" data-toggle="tab">
                                <?php echo Yii::t('app', 'Настройки') ?></a></li>
                    </ul>
                    <div class="tab-content">
                        <!-- /.tab-pane -->
                        <div class="active tab-pane" id="timeline">
                            <!-- The timeline -->
                            <ul class="timeline timeline-inverse">
                                <?php
                                foreach ($events as $event) {
                                    echo $event['event'];
                                }
                                ?>
                            </ul>
                        </div>
                        <div class="tab-pane" id="settings">
                            <div class="post">
                                <div class="user-block">
                                    <?= $this->render('_edit_users', [
                                        'action' => 'update',
                                        'model' => $model, ['class' => 'form-horizontal'],
                                        'role' => $role,
                                        'roleList' => $roleList,
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
