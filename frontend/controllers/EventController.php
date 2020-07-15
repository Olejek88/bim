<?php

namespace frontend\controllers;

use common\models\Event;
use common\models\EventType;
use common\models\Objects;
use common\models\ObjectType;
use frontend\models\EventSearch;
use Yii;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * EventController implements the CRUD actions for Event model.
 */
class EventController extends PoliterController
{
    protected $modelClass = Event::class;

    /**
     * Deletes an existing Event model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
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
     * Finds the Event model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id Id
     *
     * @return Event the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Event::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Запрашиваемая страница не существует.'));
        }
    }

    /**
     * Lists all Events models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (isset($_POST['editableAttribute'])) {
            $model = Event::find()
                ->where(['_id' => $_POST['editableKey']])
                ->one();
            if ($_POST['editableAttribute'] == 'objectUuid') {
                $model['objectUuid'] = $_POST['Event'][$_POST['editableIndex']]['objectUuid'];
            }
            if ($_POST['editableAttribute'] == 'title') {
                $model['title'] = $_POST['Event'][$_POST['editableIndex']]['title'];
            }
            if ($_POST['editableAttribute'] == 'status') {
                $model['status'] = $_POST['Event'][$_POST['editableIndex']]['status'];
            }
            if ($_POST['editableAttribute'] == 'date') {
                $model['date'] = $_POST['Event'][$_POST['editableIndex']]['date'];
            }
            if ($_POST['editableAttribute'] == 'description') {
                $model['description'] = $_POST['Event'][$_POST['editableIndex']]['description'];
            }
            if ($model->save())
                return json_encode('success');
            return json_encode('failed');
        }
        $searchModel = new EventSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 50;

        $objects = Objects::find()->where(['objectTypeUuid' => ObjectType::OBJECT])->orderBy('title')->all();
        $objects = ArrayHelper::map($objects, 'uuid', 'title');

        $types = EventType::find()->orderBy('title')->all();
        $types = ArrayHelper::map($types, 'uuid', 'title');

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'objects' => $objects,
            'types' => $types
        ]);
    }

    /**
     * функция отрабатывает сигналы от дерева и выполняет добавление нового оборудования
     *
     * @return mixed
     */
    public
    function actionNew()
    {
        $event = new Event();
        $objects = Objects::find()->where(['objectTypeUuid' => ObjectType::OBJECT])->orderBy('title')->all();
        $request = Yii::$app->request;
        $objectUuid = $request->get('objectUuid');
        return $this->renderAjax('_add_form', [
            'event' => $event,
            'objects' => $objects,
            'objectUuid' => $objectUuid
        ]);
    }

    /**
     * Creates a new model.
     * @return mixed
     */
    public
    function actionSave()
    {
        $model = new Event();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save(false)) {
                return $this->redirect('../event/index');
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

    /**
     * Lists all Events for Object
     * @return mixed
     */
    public function actionList()
    {
        if (isset($_POST['editableAttribute'])) {
            $model = Event::find()
                ->where(['_id' => $_POST['editableKey']])
                ->one();
            if ($_POST['editableAttribute'] == 'status') {
                $model['status'] = $_POST['Event'][$_POST['editableIndex']]['status'];
            }
            if ($_POST['editableAttribute'] == 'title') {
                $model['title'] = $_POST['Event'][$_POST['editableIndex']]['title'];
            }
            if ($_POST['editableAttribute'] == 'description') {
                $model['description'] = $_POST['Event'][$_POST['editableIndex']]['description'];
            }
            if ($model->save())
                return json_encode('success');
            return json_encode('failed');
        }
        $searchModel = new EventSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 50;

        $request = Yii::$app->request;
        $objectUuid = $request->get('objectUuid');

        $dataProvider->query->where(['objectUuid' => $objectUuid]);

        return $this->renderAjax('_list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'objectUuid' => $objectUuid
        ]);
    }


}
