<?php

namespace frontend\controllers;

use common\models\Parameter;
use common\models\ParameterType;
use frontend\models\ParameterSearch;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * ParameterController implements the CRUD actions for Parameter model.
 */
class ParameterController extends PoliterController
{
    protected $modelClass = Parameter::class;

    /**
     * Lists all Parameter models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ParameterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 15;
        $parameterTypes = ParameterType::find()->orderBy('title')->all();
        return $this->render(
            'index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'parameterTypes' => $parameterTypes
            ]
        );
    }

    /**
     * Deletes an existing Parameter model.
     *
     * @param integer $id Id
     *
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \Exception
     * @throws \Throwable
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Paramater model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id Id
     *
     * @return Parameter the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Parameter::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Запрашиваемая страница не существует.'));
        }
    }

    /**
     * Lists all Parameters models.
     * @param $uuid
     * @return mixed
     */
    public function actionList($uuid)
    {
        $parameters = Parameter::find()
            ->where(['uuid' => $uuid])
            ->all();
        return $this->renderAjax('_parameters_list', [
            'parameters' => $parameters
        ]);
    }

    /**
     * Creates a new Parameter model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionAdd()
    {
        $model = new Parameter();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save(false)) {
                return true;
            }
        }
        return false;
    }
}
