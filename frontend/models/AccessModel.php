<?php

namespace frontend\models;

use kartik\grid\GridView;
use Yii;
use yii\base\Model;

class AccessModel extends Model
{
    public $id;
    public $model;
    public $permission;
    public $admin = false;
    public $user = false;
    public $operator = false;
    public $description;

    function getValue($model, $role)
    {
        /** @var AccessModel $model */
        return $model->$role == false ? GridView::ICON_INACTIVE : GridView::ICON_ACTIVE;
    }

    function getDescription($name)
    {
        /** @var AccessModel $model */
        $auth = Yii::$app->authManager;
        return $auth->getPermission($name)->description;
    }

    function getNameDescription($name)
    {
        /** @var AccessModel $model */
        $auth = Yii::$app->authManager;
        return $auth->getPermission($name)->name;
    }
}