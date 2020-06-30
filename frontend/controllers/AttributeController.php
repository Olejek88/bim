<?php

namespace frontend\controllers;

use common\models\Attribute;
use frontend\models\AttributeSearch;
use Yii;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;

/**
 * AttributeController implements the CRUD actions for Attribute model.
 */
class AttributeController extends PoliterController
{
    protected $modelClass = Attribute::class;

    /**
     * Lists all Attribute models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AttributeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 15;
        return $this->render(
            'index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]
        );
    }

    /**
     * Deletes an existing Attribute model.
     *
     * @param integer $id Id
     *
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \Exception
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Attribute model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id Id
     *
     * @return Attribute the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Attribute::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Запрашиваемая страница не существует.'));
        }
    }

    /**
     * Lists all Attributes models for Attribute .
     * @param $uuid
     * @return mixed
     */
    public function actionList($uuid)
    {
        $attributes = Attribute::find()
            ->where(['uuid' => $uuid])
            ->all();
        return $this->renderAjax('_attributes_list', [
            'attributes' => $attributes,
        ]);
    }

    /**
     * Creates a new Attribute model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionAdd()
    {
        $model = new Attribute();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save(false)) {
                return true;
            }
        }
        return false;
    }
}
