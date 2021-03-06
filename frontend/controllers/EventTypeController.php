<?php

namespace frontend\controllers;

use common\models\EventType;
use common\models\ParameterType;
use Exception;
use frontend\models\EventSearchType;
use Throwable;
use Yii;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * EventTypeController implements the CRUD Events for EventType model.
 */
class EventTypeController extends PoliterController
{
    protected $modelClass = EventType::class;

    /**
     * Lists all EventType models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (isset($_POST['editableAttribute'])) {
            $model = EventType::find()
                ->where(['_id' => $_POST['editableKey']])
                ->one();
            if ($_POST['editableAttribute'] == 'title') {
                $model['title'] = $_POST['EventType'][$_POST['editableIndex']]['title'];
            }
            if ($_POST['editableAttribute'] == 'source') {
                $model['source'] = $_POST['EventType'][$_POST['editableIndex']]['source'];
            }
            if ($_POST['editableAttribute'] == 'parameterTypeUuid') {
                $model['parameterTypeUuid'] = $_POST['EventType'][$_POST['editableIndex']]['parameterTypeUuid'];
            }
            if ($_POST['editableAttribute'] == 'cnt_effect') {
                $model['cnt_effect'] = $_POST['EventType'][$_POST['editableIndex']]['cnt_effect'];
            }
            $model->save();
            return json_encode('');
        }

        $searchModel = new EventSearchType();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 50;

        $types = ParameterType::find()->where(['type' => 2])->orderBy('title')->all();
        $types = ArrayHelper::map($types, 'uuid', 'title');

        return $this->render('index', [
            'searchModel' => $searchModel,
            'types' => $types,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Deletes an existing EventType model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws Exception
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * Finds the EventType model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EventType the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EventType::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Запрашиваемая страница не существует.'));
        }
    }

    /**
     * функция отрабатывает сигналы от дерева и выполняет добавление нового оборудования
     *
     * @return mixed
     */
    public
    function actionNew()
    {
        $eventType = new EventType();
        $types = ParameterType::find()->orderBy('title DESC')->all();
        $types = ArrayHelper::map($types, 'uuid', 'title');
        return $this->renderAjax('_add_form', [
            'model' => $eventType,
            'types' => $types
        ]);
    }

    /**
     * Creates a new model.
     * @return mixed
     */
    public
    function actionSave()
    {
        $model = new EventType();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save(false)) {
                return $this->redirect('../event-type/index');
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
