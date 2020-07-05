<?php

namespace frontend\controllers;

use common\models\ObjectSubType;
use frontend\models\ObjectSubTypeSearch;
use Yii;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;

/**
 * ObjectSubTypeController implements the CRUD Attributes for ObjectSubType model.
 */
class ObjectSubTypeController extends PoliterController
{
    protected $modelClass = ObjectSubType::class;

    /**
     * Lists all ObjectSubType models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (isset($_POST['editableAttribute'])) {
            $model = ObjectSubType::find()
                ->where(['_id' => $_POST['editableKey']])
                ->one();
            if ($_POST['editableAttribute'] == 'title') {
                $model['title'] = $_POST['ObjectSubType'][$_POST['editableIndex']]['title'];
            }
            $model->save();
            return json_encode('');
        }

        $searchModel = new ObjectSubTypeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 50;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new ObjectSubType model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ObjectSubType();
        $searchModel = new ObjectSubTypeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 10;
        $dataProvider->setSort(['defaultOrder' => ['_id' => SORT_DESC]]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
                'dataProvider' => $dataProvider
            ]);
        }
    }

    /**
     * Deletes an existing ObjectSubType model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws StaleObjectException
     * @throws \Throwable
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * Finds the ObjectSubType model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ObjectSubType the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ObjectSubType::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Запрашиваемая страница не существует.'));
        }
    }

    /**
     * функция отрабатывает сигналы от дерева и выполняет добавление нового типа
     *
     * @return mixed
     */
    public
    function actionNew()
    {
        $objectType = new ObjectSubType();
        return $this->renderAjax('_add_form', [
            'model' => $objectType
        ]);
    }

    /**
     * Creates a new model.
     * @return mixed
     */
    public
    function actionSave()
    {
        $model = new ObjectSubType();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save(false)) {
                return $this->redirect('../object-sub-type/index');
            } else {
                $message = '';
                foreach ($model->errors as $key => $error) {
                    $message .= $error[0] . '</br>';
                }
                return json_encode(['message' => $message]);
            }
        }
        return $this->render('_add_form', [
            'model' => $model
        ]);
    }

}
