<?php

namespace frontend\controllers;

use common\models\ServiceRegister;
use frontend\models\ServiceRegisterSearch;
use Yii;

/**
 * ServiceRegisterController implements the CRUD actions for ServiceRegister model.
 */
class ServiceRegisterController extends PoliterController
{
    protected $modelClass = ServiceRegister::class;

    /**
     * Lists all ServiceRegister models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ServiceRegisterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 50;

        /** @var ServiceRegister[] $serviceRegisters */
        $serviceRegisters = $dataProvider->getModels();
        $viewed = [];
        foreach ($serviceRegisters as $serviceRegister) {
            if ($serviceRegister->view == 0) {
                $viewed[] = $serviceRegister->uuid;
            }
        }

        if (count($viewed) > 0) {
            ServiceRegister::updateAll(['view' => 1], ['uuid' => $viewed]);
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

}
