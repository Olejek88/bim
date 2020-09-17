<?php

namespace frontend\controllers;

use common\models\Event;
use common\models\EventType;
use common\models\Objects;
use common\models\ObjectType;
use Exception;
use frontend\models\EventSearch;
use Throwable;
use Yii;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\NotFoundHttpException;
use yii\web\Response;

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
                if ($_POST['Event'][$_POST['editableIndex']]['status'] == 1 && $model['status'] == 0) {
                    $model['dateFact'] = date("Y-m-d H:i:s");
                }
            }
            if ($_POST['editableAttribute'] == 'date') {
                $model['date'] = $_POST['Event'][$_POST['editableIndex']]['date'];
            }
            if ($_POST['editableAttribute'] == 'dateFact') {
                $model['dateFact'] = $_POST['Event'][$_POST['editableIndex']]['dateFact'];
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
        if (isset($_GET['uuid'])) {
            $dataProvider->query->where(['uuid' => $_GET['uuid']]);
        }
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
        $objects = ArrayHelper::map($objects, 'uuid', function ($data) {
            return $data->getFullTitle();
        });
        $request = Yii::$app->request;
        $objectUuid = $request->get('objectUuid');
        $types = EventType::find()->orderBy('title')->all();
        $types = ArrayHelper::map($types, 'uuid', 'title');

        return $this->renderAjax('_add_form', [
            'event' => $event,
            'objects' => $objects,
            'types' => $types,
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
                return $this->redirect($_SERVER['HTTP_REFERER']);
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

        $dateStart = $request->get('dateStart');
        $dateEnd = $request->get('dateEnd');
        if ($dateStart)
            $dataProvider->query->andWhere(['>=', 'date', $dateStart]);
        if ($dateEnd)
            $dataProvider->query->andWhere(['<=', 'date', $dateEnd]);

        return $this->renderAjax('_list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'objectUuid' => $objectUuid
        ]);
    }

    /**
     * @return string
     */
    public
    function actionPlan()
    {
        setlocale(LC_TIME, 'ru_RU.UTF-8', 'Russian_Russia', 'Russian');
        $objects = [];
        $dates_title = [];
        $mon_date_str = [];
        $mon_date_str2[] = [];
        $mon_date_str3[] = [];

        $month_count = 1;
        $dates = date("Y0101 00:00:00", time());
        while ($month_count <= 12) {
            $mon_date[$month_count] = strtotime($dates);
            $mon_date_str[$month_count] = strftime("%Y%m01000000", $mon_date[$month_count]);
            $mon_date_str2[$month_count] = strftime("%Y%m01000000", $mon_date[$month_count]);
            $dates_title[$month_count] = strftime("%h", $mon_date[$month_count]);

            $localtime = localtime($mon_date[$month_count], true);
            $mon = $localtime['tm_mon'];
            $year = $localtime['tm_year'];
            $mon++;
            if ($mon > 11) {
                $mon = 0;
                $year++;
            }
            $dates = sprintf("%d-%02d-01 00:00:00", $year + 1900, $mon + 1);
            $mon_date_str3[$month_count] = strftime("%Y%m01000000", strtotime($dates));
            $month_count++;
        }
        $count = 0;
        $events = Event::find()->all();
        foreach ($events as $event) {
            for ($i = 1; $i < $month_count; $i++) {
                $sum[$i] = 0;
            }
            $objects[$count]['title'] = $event->object->getFullTitle();
            $objects[$count]['event'] = $event->title;
            for ($month = 1; $month < $month_count; $month++) {
                $objects[$count]['plans'][$month]['plan'] = '';
                $parameter_uuid = null;
                $objects[$count]['plans'][$month]['fact'] = '';
                $date = strftime("%Y%m01000000", strtotime($event->date));
                if ($date == $mon_date_str[$month]) {
                    $objects[$count]['plans'][$month]['plan']
                        = Html::a('<span class="span-plan2">' . date("d/m", strtotime($event->date)) . '</span>',
                        ['/event/plan-edit', 'event_uuid' => $event['uuid'], 'plan' => 1],
                        [
                            'title' => 'Редактировать',
                            'data-toggle' => 'modal',
                            'data-target' => '#modalPlan',
                        ]);
                    $factDate = '-';
                    if ($event->dateFact) {
                        $factDate = date("d/m", strtotime($event->dateFact));
                    }
                    $objects[$count]['plans'][$month]['fact']
                        = Html::a('<span class="span-plan0">' . $factDate . '</span>',
                        ['/event/plan-edit', 'event_uuid' => $event['uuid'], 'plan' => 0],
                        [
                            'title' => 'Редактировать',
                            'data-toggle' => 'modal',
                            'data-target' => '#modalPlan',
                        ]);
                }
            }
            $count++;
        }
        return $this->render('plan', [
            'objects' => $objects,
            'month_count' => $month_count,
            'dates' => $dates_title
        ]);
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public
    function actionPlanEdit()
    {
        if (isset($_GET["event_uuid"])) {
            $event = Event::find()->where(['uuid' => $_GET["event_uuid"]])->one();
            if ($event) {
                return $this->renderAjax('_add_plan', [
                    'event' => $event,
                    'plan' => $_GET['plan']
                ]);
            }
        }
        return null;
    }

    /**
     * Creates a new model.
     * @return mixed
     */
    public
    function actionChange()
    {
        if (isset($_POST["eventUuid"])) {
            $model = Event::find()->where(['uuid' => $_POST["eventUuid"]])->one();
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save(false)) {
                    return $this->redirect('../event/plan');
                } else {
                    $message = '';
                    foreach ($model->errors as $key => $error) {
                        $message .= $error[0] . '</br>';
                    }
                    return json_encode(['message' => $message]);
                }
            }
        }
        return $this->redirect('../event/plan');

    }

    /**
     * @return string
     */
    public function actionCalendar()
    {
        return $this->render('calendar');
    }

    /**
     * Метод возвращает события календарю, за указанный период.
     *
     * @param null $start
     * @param null $end
     * @param null $_
     * @return array
     */
    public
    function actionJsoncalendar($start = NULL, $end = NULL, $_ = NULL)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $tsStart = strtotime($start);
        $tsEnd = strtotime($end);

        $eventTs = Event::find()
            ->andWhere([
                    'and',
                    ['>=', 'date', date('Y-m-d H:i:s', $tsStart)],
                    ['<', 'date', date('Y-m-d H:i:s', $tsEnd)],
                ]
            )
            ->andWhere(['deleted' => 0])
            ->orderBy("_id DESC")
            ->all();

        $events = [];
        /** @var Event $event */
        foreach ($eventTs as $eventT) {
            $event = new \yii2fullcalendar\models\Event();
            $event->id = $eventT['_id'];
            $event->title = '[' . $eventT['object']->getFullTitle() . '] ' . $eventT['title'];

            if ($eventT->dateFact) {
                $event->start = $eventT->date;
                $event->end = $eventT->dateFact;
            } else {
                $event->start = $eventT->date;
                $event->end = $eventT->date;
            }

            if ($eventT->status == 0) {
                $event->backgroundColor = 'grey';
            } else {
                $event->backgroundColor = 'green';
            }
            $event->url = '/event/index?uuid=' . $eventT["uuid"];
            $event->color = '#333333';
            $events[] = $event;
        }

        return $events;
    }

}
