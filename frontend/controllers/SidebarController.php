<?php

namespace frontend\controllers;


use common\models\Event;
use common\models\Objects;
use common\models\ServiceRegister;
use common\models\User;
use Yii;
use yii\web\NotFoundHttpException;


$accountUser = Yii::$app->user->identity;
$currentUser = User::findOne(['id' => $accountUser['id']]);
if ($currentUser == null) {
    /** @noinspection PhpUnhandledExceptionInspection */
    throw new NotFoundHttpException(Yii::t('app', 'Пользователь не найден!'));
}

Yii::$app->view->params['currentUser'] = $currentUser;

$userImage = $currentUser->getImageUrl();
if (!$userImage)
    $userImage = Yii::$app->request->baseUrl . '/images/unknown2.png';
$userImage = str_replace("storage", "files", $userImage);

$today = date("Y-m-d H:i:s", time());
$today30 = date("Y-m-d H:i:s", time() + 30 * 24 * 3600);

$events_near = Event::find()
    ->where(['<', 'createdAt', $today30])
    ->orderBy('createdAt')
    ->all();


Yii::$app->view->params['userImage'] = $userImage;

Yii::$app->view->params['events'] = $events_near;

Yii::$app->view->params['register_unread'] = ServiceRegister::find()
    ->where(['=', 'view', 0])
    ->andWhere(['type' => ServiceRegister::TYPE_ERROR])
    ->count();

$deleted_objects = Objects::find()
    ->where(['=', 'deleted', 1])
    ->count();

Yii::$app->view->params['deleted'] = $deleted_objects;

