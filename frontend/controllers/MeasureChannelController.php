<?php

namespace frontend\controllers;

use common\datasource\DataSourceTrait;
use common\datasource\IDataSource;
use common\models\Measure;
use common\models\MeasureChannel;
use common\models\MeasureType;
use common\models\Objects;
use common\models\ObjectType;
use common\models\User;
use Exception;
use frontend\models\MeasureChannelSearch;
use Throwable;
use Yii;
use yii\base\Module;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * MeasureChannelController implements the CRUD actions for MeasureChannel model.
 */
class MeasureChannelController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    // отключаем проверку для внешних запросов

    /**
     * @param $action
     * @return bool
     * @throws BadRequestHttpException
     */
    public function beforeAction($action)
    {
        if ($action->id === 'move') {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    /**
     * Lists all Measure models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MeasureChannelSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $objects = Objects::find()->where(['objectTypeUuid' => ObjectType::OBJECT])->orderBy('title DESC')->all();
        $types = MeasureType::find()->orderBy('title DESC')->all();
        return $this->render(
            'index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'types' => $types,
                'objects' => $objects
            ]
        );
    }

    /**
     * функция отрабатывает сигналы от дерева и выполняет добавление нового типа
     *
     * @return mixed
     */
    public
    function actionNew()
    {
        $measureChannel = new MeasureChannel();
        $objects = Objects::find()->where(['objectTypeUuid' => ObjectType::OBJECT])->orderBy('title DESC')->all();
        $types = MeasureType::find()->orderBy('title DESC')->all();
        $objects = ArrayHelper::map($objects, 'uuid', function ($data) {
            /** @var Objects $data */
            return $data->getFullTitle();
        });
        $types = ArrayHelper::map($types, 'uuid', 'title');
        // получаем список источников
        $dataSources = [];
        $modules = Yii::$app->getModules();
        foreach ($modules as $prefix => $module) {
            /** @var $module IDataSource|DataSourceTrait|Module */
            if (is_array($module) && !empty($module['class']) && str_contains($module['class'], 'common\datasource\\')) {
                $module = Yii::$app->getModule($prefix);
            } else if (is_object($module) && str_contains($module::className(), 'common\datasource\\')) {
            } else {
                continue;
            }

            $dataSources[$module->id] = $module->description;
        }

        return $this->renderAjax('_add_sensor_channel', [
            'model' => $measureChannel,
            'types' => $types,
            'object_uuid' => null,
            'objects' => $objects,
            'dataSources' => $dataSources,
        ]);
    }

    /**
     * функция рисует график и таблицу значений канала
     *
     * @return mixed
     */
    public
    function actionTrend()
    {
        $request = Yii::$app->request;
        $measureChannelUuid = $request->get('measureChannelUuid');
        if ($measureChannelUuid) {
            /** @var MeasureChannel $measureChannel */
            $measureChannel = MeasureChannel::find()->where(['uuid' => $measureChannelUuid])->limit(1)->one();
            if ($measureChannel) {
                $name = $measureChannel->title;
                $values = Measure::find()
                    ->where(['measureChannelId' => $measureChannel['_id']])
                    ->orderBy('date desc')
                    ->limit(100)
                    ->all();
                return $this->renderAjax('trend', [
                    'model' => $measureChannel,
                    'name' => $name,
                    'values' => array_reverse($values)
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
    function actionSave()
    {
        if (isset($_POST['channelUuid'])) {
            $model = MeasureChannel::find()->where(['uuid' => $_POST['channelUuid']])->limit(1)->one();
        } else {
            $model = new MeasureChannel();
        }
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save(false)) {
                return $this->redirect(parse_url($_SERVER["HTTP_REFERER"], PHP_URL_PATH) . '?node=' . $model['_id'] . 'k');
            } else {
                $message = '';
                foreach ($model->errors as $key => $error) {
                    $message .= $error[0] . '</br>';
                }
                return json_encode(['message' => $message]);
            }
        }

        return $this->render('_add_sensor_channel', [
            'model' => $model
        ]);
    }

    /**
     * Finds the MeasureChannel model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id Id
     *
     * @return MeasureChannel the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MeasureChannel::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Deletes an existing MeasureChannel model.
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
        if (!Yii::$app->user->can(User::PERMISSION_ADMIN)) {
            return $this->redirect('/site/index');
        }

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @return mixed
     */
    public
    function actionDashboard()
    {
        $request = Yii::$app->request;
        $measureChannelUuid = $request->get('uuid');
        if ($measureChannelUuid) {
            /** @var MeasureChannel $measureChannel */
            $measureChannel = MeasureChannel::find()->where(['uuid' => $measureChannelUuid])->limit(1)->one();
            if ($measureChannel) {
                $name = $measureChannel->title;
                $measures = Measure::find()
                    ->where(['measureChannelId' => $measureChannel['_id']])
                    ->orderBy('date')
                    ->limit(30)
                    ->all();
                $measures_reverse = array_reverse($measures);

                $values = '';
                $values .= "{ name: 'Тренд',";
                $values .= "data: [";
                $zero = 0;
                $categories = "";
                foreach ($measures as $measure) {
                    $categories .= '\'' . date("m/Y", strtotime($measure->date)) . '\',';
                    if ($zero > 0) {
                        $values .= ",";
                    }
                    $values .= $measure->value;
                    $zero++;
                }
                $categories = substr($categories, 0, -1);
                $values .= "]}";

                return $this->render('dashboard', [
                    'channel' => $measureChannel,
                    'name' => $name,
                    'categories' => $categories,
                    'values' => $values,
                    'measures' => $measures_reverse
                ]);
            }
        }
        return null;
    }
}
