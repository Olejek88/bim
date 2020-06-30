<?php

namespace frontend\controllers;

use common\models\ActionRegister;
use frontend\models\ActionRegisterSearch;
use Yii;

/**
 * ActionRegisterController implements the CRUD actions for ActionRegister model.
 */
class ActionRegisterController extends PoliterController
{
    protected $modelClass = ActionRegister::class;

    /**
     * Lists all ActionRegister models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ActionRegisterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 50;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

}
