<?php

namespace frontend\controllers;

use common\models\Register;
use frontend\models\RegisterSearch;
use Yii;

/**
 * RegisterController implements the CRUD actions for Register model.
 */
class RegisterController extends PoliterController
{
    protected $modelClass = Register::class;

    /**
     * Lists all ActionRegister models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RegisterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 50;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

}
