<?php

namespace frontend\controllers;

use common\models\User;
use frontend\models\AccessSearch;
use kartik\grid\GridView;
use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

class AccessController extends Controller
{
    /**
     * @param $permission
     * @return mixed
     */
    public static function getCoolText($permission)
    {
        $a = [
            'create' => Yii::t('app', 'создание'),
            'index' => Yii::t('app', 'просмотр списка'),
            'view' => Yii::t('app', 'просмотр'),
            'update' => Yii::t('app', 'изменение'),
            'delete' => Yii::t('app', 'удаление'),
            'access' => Yii::t('app', 'доступ'),
        ];

        if (isset($a[$permission])) {
            return $a[$permission];
        } else {
            return $permission;
        }
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
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
     * @return string
     * @throws \Exception
     */
    public function actionUpdate()
    {
        $request = Yii::$app->request;
        $model = $request->getBodyParam('model', null);
        $permission = $request->getBodyParam('permission', null);
        $permission .= $model;
        $accessModel = $request->getBodyParam('AccessModel', null);
        $editableIndex = $request->getBodyParam('editableIndex', null);
        $editableAttribute = $request->getBodyParam('editableAttribute', null);
        $newAttributeValue = $accessModel[$editableIndex][$editableAttribute];

        $am = Yii::$app->getAuthManager();
        $role = $am->getRole($editableAttribute);
        $permission = $am->getPermission($permission);

        if ($editableAttribute == 'description') {
            $permission->description = $newAttributeValue;
            $am->update($permission->name, $permission);
            $output = ['output' => '', 'message' => '']; //Empty json for kartik response
        } else {

            if ($newAttributeValue == 0) {
                $am->removeChild($role, $permission);
                $output = ['output' => GridView::ICON_INACTIVE];
            } else {
                try {
                    $am->addChild($role, $permission);
                } catch (Exception $e) {

                }
                $output = ['output' => GridView::ICON_ACTIVE];
            }
        }

        return json_encode($output);
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new AccessSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 100;

        return $this->render('index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]
        );
    }

    /**
     * @param $action
     * @return bool
     * @throws BadRequestHttpException
     */
    public function beforeAction($action)
    {
        if (!Yii::$app->getUser()->isGuest) {
            if (!Yii::$app->user->can(User::PERMISSION_ADMIN)) {
                Yii::$app->session->setFlash('warning', '<h3>' . Yii::t('app', 'Не достаточно прав доступа.') . '</h3>');
                $this->goHome();
                return false;
            }
        }

        return parent::beforeAction($action);
    }
}
