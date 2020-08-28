<?php


namespace common\datasource\vega\controllers;

use frontend\controllers\PoliterController;
use Yii;
use yii\filters\AccessControl;

/**
 *
 * @property-read array|string[][] $permissions
 */
class VegaController extends PoliterController
{
    protected $modelClass = \common\datasource\vega\models\VegaController::class;

    /**
     * {@inheritdoc}
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
     */
    public function actionIndex()
    {
        // для того чтоб правильно формировались url, редиректим на полный путь модуля
        if (Yii::$app->controller->id == 'default') {
            return Yii::$app->response->redirect('/' . $this->module->id . '/vega/index');
        }

        // TODO: запрос всех устройств/данных с сервера
        $data = [];
        return $this->render('index', [
            'data' => $data,
        ]);
    }

    public function actionTable()
    {
        return $this->render('table', [
        ]);
    }

}