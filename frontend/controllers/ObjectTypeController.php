<?php

namespace frontend\controllers;

use common\models\ObjectType;
use frontend\models\ObjectSearchType;
use Throwable;
use Yii;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;

/**
 * ObjectTypeController implements the CRUD Attributes for ObjectType model.
 */
class ObjectTypeController extends PoliterController
{
    protected $modelClass = ObjectType::class;

    /**
     * Lists all ObjectType models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (isset($_POST['editableAttribute'])) {
            $model = ObjectType::find()
                ->where(['_id' => $_POST['editableKey']])
                ->one();
            if ($_POST['editableAttribute'] == 'title') {
                $model['title'] = $_POST['ObjectType'][$_POST['editableIndex']]['title'];
            }
            $model->save();
            return json_encode('');
        }

        $searchModel = new ObjectSearchType();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 50;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Deletes an existing ObjectType model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the AttributeType model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ObjectType the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ObjectType::findOne($id)) !== null) {
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
        $objectType = new ObjectType();
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
        $model = new ObjectType();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save(false)) {
                return $this->redirect('../object-type/index');
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
