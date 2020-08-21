<?php

namespace frontend\controllers;

use common\models\PoliterModel;
use Exception;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

class PoliterController extends Controller
{
    protected $modelClass;

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }


    /**
     * @param $action
     * @return bool
     * @throws BadRequestHttpException
     */
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            $access = false;
            try {
                /* @var PoliterModel $modelClass */
                $modelClass = $this->modelClass;
                /* @var PoliterModel $model */
                $model = new $modelClass;
                $modelPermissions = $model->permissions;
                $permiss = null; //Проверка на массив/строку для описания прав
                if (isset($modelPermissions[$action->id])) {
                    if (is_array($modelPermissions[$action->id])) {
                        $permiss = $modelPermissions[$action->id]['name'];
                    } else {
                        $permiss = $modelPermissions[$action->id];
                    }

                    if (Yii::$app->user->can($permiss))
                        $access = true;
                }
            } catch (Exception $e) {
                Yii::error($e->getMessage(), 'frontend/controllers/PoliterController.php');
            }

            if (!$access) {
                Yii::$app->session->setFlash('warning', '<h3>' .
                    Yii::t('app', 'Не достаточно прав доступа.') . '</h3>');
                $this->redirect('/');
            }

            return $access;
        } else {
            return false;
        }
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function checkDelete($uuid)
    {
        /*        $hardReference = HardReference::find()->where(['entityUuid' => $uuid])->one();
                if ($hardReference) {
                    Yii::$app->session->setFlash('warning', Yii::t('app', 'Системный элемент справочника, удалить/отредактировать нельзя'));
                    return false;
                }*/
        return true;
    }
}