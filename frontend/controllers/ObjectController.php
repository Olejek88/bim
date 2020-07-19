<?php

namespace frontend\controllers;

use common\components\MainFunctions;
use common\models\DistrictCoordinates;
use common\models\MeasureChannel;
use common\models\MeasureType;
use common\models\Objects;
use common\models\ObjectSubType;
use common\models\ObjectType;
use frontend\models\ObjectsSearch;
use Throwable;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\NotFoundHttpException;

/**
 * ObjectController implements the CRUD actions for Object model.
 */
class ObjectController extends PoliterController
{
    protected $modelClass = Objects::class;

    /**
     * Return average coordinates around all objects
     * @return array
     */
    public static function getAverageCoordinates()
    {
        $sum_latitude = 0;
        $sum_longitude = 0;
        $count = 0;
        $objects = Objects::find()
            ->where('latitude > 0')
            ->andWhere('longitude > 0')
            ->asArray()
            ->all();
        foreach ($objects as $object) {
            $sum_latitude += $object['latitude'];
            $sum_longitude += $object['longitude'];
            $count++;
        }
        if ($count > 0) {
            $sum_longitude /= $count;
            $sum_latitude /= $count;
        }
        $av['latitude'] = $sum_latitude;
        $av['longitude'] = $sum_longitude;
        return $av;
    }

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

    /**
     * Lists all Object models.
     * @return mixed
     * @throws InvalidConfigException
     */
    public function actionIndex()
    {
        $searchModel = new ObjectsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 200;
        $objectTypes = ObjectType::find()->orderBy('title desc')->all();
        $objectSubTypes = ObjectSubType::find()->orderBy('title desc')->all();

        $objectSubTypes = ArrayHelper::map($objectSubTypes, 'uuid', 'title');
        $objectTypes = ArrayHelper::map($objectTypes, 'uuid', 'title');

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'objectTypes' => $objectTypes,
            'objectSubTypes' => $objectSubTypes
        ]);
    }

    /**
     * Displays a single Object model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Finds the Object model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Objects
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Objects::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Creates a new Flat model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws InvalidConfigException
     */
    public function actionCreate()
    {
        $model = new Objects();
        $searchModel = new ObjectsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 50;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $searchModel = new ObjectsSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            $dataProvider->pagination->pageSize = 15;
            //if ($_GET['from'])
            return $this->render('table', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        } else {
            return $this->render('create', [
                'model' => $model, 'dataProvider' => $dataProvider
            ]);
        }
    }

    /**
     * Updates an existing Object model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Object model.
     * If deletion is successful, the browser will be redirected to the 'table' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($id)
    {
        $object = $this->findModel($id);
        if ($object) {
            $object->deleted = true;
            $object->save();
        }
        return $this->redirect(parse_url($_SERVER["HTTP_REFERER"], PHP_URL_PATH) . '?node=' . $object['_id'] . 'k');
    }

    /**
     * функция отрабатывает сигналы от дерева и выполняет отвязывание выбранного оборудования от пользователя
     * @return mixed
     */
    public function actionDeleted()
    {
        $request = Yii::$app->request;
        $selected_node = $request->post('selected_node');
        $type = $request->post('type');

        if ($selected_node && $type) {
            if (is_numeric($selected_node)) {
                if ($type == 'channel') {
                    $channelById = MeasureChannel::find()->where(['_id' => $selected_node])
                        ->andWhere(['deleted' => 0])
                        ->limit(1)
                        ->one();
                    if ($channelById) {
                        $channelById['deleted'] = true;
                        $channelById->save();
                        $return['code'] = 0;
                        $return['message'] = '';
                        return json_encode($return);
                    }
                } else {
                    $objectById = Objects::find()->where(['_id' => $selected_node])
                        ->andWhere(['deleted' => 0])
                        ->limit(1)
                        ->one();
                    if ($objectById) {
                        $objectById['deleted'] = true;
                        $objectById->save();
                        $return['code'] = 0;
                        $return['message'] = '';
                        return json_encode($return);
                    }
                }
            }
        }
        $return['code'] = -1;
        $return['message'] = 'Неправильно заданы параметры';
        return json_encode($return);
    }

    /**
     * Build tree of equipment by user
     *
     * @return mixed
     */
    public function actionTree()
    {
        $search = (isset($_GET['sq']) && !empty($_GET['sq'])) ? $_GET['sq'] : null;

        $fullTree = array();
        $districts = Objects::find()
            ->where(['objectTypeUuid' => ObjectType::DISTRICT])
            ->andWhere(['deleted' => 0])
            ->orderBy('title')
            ->all();
        foreach ($districts as $district) {
            $fullTree['children'][] = [
                'title' => $district['title'],
                'key' => $district['_id'],
                'type' => 'district',
                'expanded' => true,
                'folder' => true
            ];
            $cities = Objects::find()
                ->where(['objectTypeUuid' => ObjectType::CITY])
                ->andWhere(['parentUuid' => $district['uuid']])
                ->andWhere(['deleted' => 0])
                ->orderBy('title')
                ->all();
            foreach ($cities as $city) {
                $childIdx = count($fullTree['children']) - 1;
                $fullTree['children'][$childIdx]['children'][] = [
                    'title' => $city['title'],
                    'key' => $city['_id'],
                    'type' => 'city',
                    'expanded' => true,
                    'folder' => true
                ];
                $city_districts = Objects::find()
                    ->where(['objectTypeUuid' => ObjectType::CITY_DISTRICT])
                    ->andWhere(['parentUuid' => $city['uuid']])
                    ->andWhere(['deleted' => 0])
                    ->all();
                foreach ($city_districts as $city_district) {
                    $childIdx2 = count($fullTree['children'][$childIdx]['children']) - 1;
                    $fullTree['children'][$childIdx]['children'][$childIdx2]['children'][] = [
                        'title' => $city_district['title'],
                        'type' => 'city_district',
                        'key' => $city_district['_id'],
                        'expanded' => true,
                        'folder' => true
                    ];
                    $streets = Objects::find()
                        ->where(['objectTypeUuid' => ObjectType::STREET])
                        ->andWhere(['parentUuid' => $city_district['uuid']])
                        ->andWhere(['deleted' => 0])
                        ->all();
                    foreach ($streets as $street) {
                        $childIdx3 = count($fullTree['children'][$childIdx]['children'][$childIdx2]['children']) - 1;
                        $fullTree['children'][$childIdx]['children'][$childIdx2]['children'][$childIdx3]['children'][] = [
                            'title' => $street['title'],
                            'type' => 'street',
                            'key' => $street['_id'],
                            'folder' => true
                        ];
                        $objects = Objects::find()
                            ->where(['objectTypeUuid' => ObjectType::OBJECT])
                            ->andWhere(['parentUuid' => $street['uuid']])
                            ->andWhere(['deleted' => 0])
                            ->all();
                        foreach ($objects as $object) {
                            $childIdx4 = count($fullTree['children'][$childIdx]['children'][$childIdx2]['children'][$childIdx3]['children']) - 1;
                            $links = Html::a('<span class="fa fa-list"></span>&nbsp',
                                ['/event/list', 'objectUuid' => $object['uuid']],
                                [
                                    'title' => Yii::t('app', 'События объекта'),
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modalRegister',
                                ]
                            );
                            $links .= Html::a('<span class="fa fa-anchor"></span>&nbsp',
                                ['/parameter/list', 'uuid' => $object['uuid']],
                                [
                                    'title' => Yii::t('app', 'Параметры объекта'),
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modalParameter',
                                ]
                            );

                            $fullTree['children'][$childIdx]['children'][$childIdx2]['children'][$childIdx3]['children'][$childIdx4]['children'][] = [
                                'title' => $object->getFullTitle(),
                                'type' => 'object',
                                'links' => $links,
                                'type_title' => $object->objectSubType->title,
                                'key' => $object['_id'],
                                'folder' => true
                            ];
                            /** @var MeasureChannel[] $channels */
                            $channels = MeasureChannel::find()
                                ->where(['objectUuid' => $object['uuid']])
                                ->andWhere(['deleted' => 0])
                                ->all();
                            foreach ($channels as $channel) {
                                $childIdx5 = count($fullTree['children'][$childIdx]['children'][$childIdx2]['children'][$childIdx3]['children'][$childIdx4]['children']) - 1;
                                $links = Html::a('<span class="fa fa-plus-square"></span>&nbsp',
                                    ['/measure/new', 'measureChannelUuid' => $channel['uuid']],
                                    [
                                        'title' => Yii::t('app', 'Добавить измерение'),
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modalAddMeasure',
                                    ]
                                );
                                $links .= Html::a('<span class="fa fa-line-chart"></span>&nbsp',
                                    ['/measure-channel/trend', 'measureChannelUuid' => $channel['uuid']],
                                    [
                                        'title' => Yii::t('app', 'Измерения'),
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modalChart',
                                    ]
                                );
                                $value = $channel->getLastMeasure() . "&nbsp;" . $links;
                                $links = "";

                                $fullTree['children'][$childIdx]['children'][$childIdx2]['children'][$childIdx3]['children'][$childIdx4]['children'][$childIdx5]['children'][] = [
                                    'title' => $channel->title,
                                    'type' => 'channel',
                                    'type_title' => $channel->measureType->title,
                                    'value' => $value,
                                    'measure_type' => $channel->getTypeName(),
                                    'original' => $channel->original_name,
                                    'parameter' => $channel->param_id,
                                    'key' => $channel['_id'] . 'k',
                                    'links' => $links,
                                    'folder' => false
                                ];
                            }
                        }
                    }
                }
            }
        }
        return $this->render(
            'tree',
            [
                'objects' => $fullTree,
                'sq' => $search
            ]
        );
    }

    /**
     * функция отрабатывает сигналы от дерева и выполняет добавление нового оборудования или объекта
     *
     * @return mixed
     */
    public function actionNew()
    {
        $object = new Objects();
        $objectTypes = ObjectType::find()->orderBy('title desc')->all();
        $objectSubTypes = ObjectSubType::find()->orderBy('title desc')->all();
        $objects = Objects::find()->orderBy('title desc')->all();

        $objectSubTypes = ArrayHelper::map($objectSubTypes, 'uuid', 'title');
        $objectTypes = ArrayHelper::map($objectTypes, 'uuid', 'title');
        $objects = ArrayHelper::map($objects, 'uuid', 'title');

        $object_uuid = null;
        $object_type = null;
        if (!empty($_POST['selected_node'])) {
            /** @var Objects $currentObject */
            $currentObject = Objects::find()->where(['_id' => $_POST['selected_node']])->one();
            if ($currentObject) {
                $object_uuid = $currentObject['uuid'];
                $object_type = $currentObject->getChildObjectType();
                if ($object_type == 1) {
                    $measureChannel = new MeasureChannel();
                    $types = MeasureType::find()->orderBy('title DESC')->all();
                    $types = ArrayHelper::map($types, 'uuid', 'title');

                    return $this->renderAjax('../measure-channel/_add_sensor_channel', [
                        'model' => $measureChannel,
                        'types' => $types,
                        'object_uuid' => $object_uuid,
                        'objects' => null
                    ]);
                }
            }
        }
        //return 'Нельзя добавить объект в этом месте';

        return $this->renderAjax('_add_form', [
            'object' => $object,
            'objects' => $objects,
            'objectSubTypes' => $objectSubTypes,
            'objectTypes' => $objectTypes,
            'object_uuid' => $object_uuid,
            'object_type' => $object_type
        ]);
    }

    /**
     * функция отрабатывает сигналы от дерева и выполняет редактирование оборудования
     *
     * @return mixed
     */
    public function actionEdit()
    {
        $objectTypes = ObjectType::find()->orderBy('title desc')->all();
        $objectSubTypes = ObjectSubType::find()->orderBy('title desc')->all();
        $objects = Objects::find()->orderBy('title desc')->all();

        $objectSubTypes = ArrayHelper::map($objectSubTypes, 'uuid', 'title');
        $objectTypes = ArrayHelper::map($objectTypes, 'uuid', 'title');
        $objects = ArrayHelper::map($objects, 'uuid', 'title');

        $request = Yii::$app->request;
        $type = $request->post('type');

        $object_uuid = null;
        $object_type = null;
        if ($_POST['selected_node'] && $type) {
            /** @var Objects $currentObject */
            $object = Objects::find()->where(['_id' => $_POST['selected_node']])->one();
            if ($type == 'object' && $object) {
                return $this->renderAjax('_add_form', [
                    'object' => $object,
                    'objects' => $objects,
                    'objectSubTypes' => $objectSubTypes,
                    'objectTypes' => $objectTypes,
                    'object_type' => $object_type
                ]);
            }

            /** @var MeasureChannel $measureChannel */
            $measureChannel = MeasureChannel::find()->where(['_id' => $_POST['selected_node']])->one();
            if ($type == 'channel' && $measureChannel) {
                $types = MeasureType::find()->orderBy('title DESC')->all();
                $types = ArrayHelper::map($types, 'uuid', 'title');
                return $this->renderAjax('../measure-channel/_add_sensor_channel', [
                    'model' => $measureChannel,
                    'types' => $types,
                    'object_uuid' => $object_uuid,
                    'objects' => null
                ]);
            }
        }
    }

    /**
     * Creates a new Object model.
     * @return mixed
     */
    public function actionSave()
    {
        if (isset($_POST['objectUuid']))
            $model = Objects::find()->where(['uuid' => $_POST['objectUuid']])->limit(1)->one();
        else
            $model = new Objects();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save(false)) {
                return $this->redirect(parse_url($_SERVER["HTTP_REFERER"], PHP_URL_PATH) . '?node=' . $model['_id'] . 'k');
            } else {
                $return['code'] = -1;
                $return['message'] = json_encode($model->errors);
                return json_encode($return);
            }
        }
        return false;
    }

    /**
     * Creates a new Object district model.
     * @return mixed
     */
    public function actionNewDistrict()
    {
        $objectTypeUuid = ObjectType::SUB_DISTRICT;
        $objectSubTypeUuid = ObjectSubType::GENERAL;
        $objects = Objects::find()->where(['objectTypeUuid' => ObjectType::CITY])->orderBy('title desc')->all();
        $objects = ArrayHelper::map($objects, 'uuid', 'title');

        $request = Yii::$app->request;
        $latlng = $request->post('latlng');

        $object_uuid = null;
        $object_type = null;
        if ($latlng) {
            $object = new Objects();
            return $this->renderAjax('_add_sub_district', [
                'object' => $object,
                'objects' => $objects,
                'latlng' => $latlng,
                'objectSubTypeUuid' => $objectSubTypeUuid,
                'objectTypeUuid' => $objectTypeUuid
            ]);
        }
        return null;
    }

    /**
     * Creates a new Object custom District model.
     * @return mixed
     * @throws \Exception
     */
    public function actionSaveDistrict()
    {
        $model = new Objects();
        $request = Yii::$app->request;
        $latlng = $request->post('latlng');
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save(false)) {
                $district = new DistrictCoordinates();
                $district->uuid = MainFunctions::GUID();
                $district->districtUuid = $model->uuid;
                $district->coordinates = $latlng;
                if ($district->save()) {
                    return $this->redirect(parse_url($_SERVER["HTTP_REFERER"], PHP_URL_PATH) . '?node=' . $model['_id'] . 'k');
                } else {
                    $return['code'] = -1;
                    $return['message'] = json_encode($district->errors);
                    return json_encode($return);
                }
            } else {
                $return['code'] = -1;
                $return['message'] = json_encode($model->errors);
                return json_encode($return);
            }
        }
        return false;
    }

    /**
     * Restore an existing Objects model.
     *
     * @return mixed
     */
    public
    function actionRestore()
    {
        if (isset($_GET['uuid'])) {
            $object = Objects::find()->where(['uuid' => $_GET['uuid']])->one();
            if ($object) {
                $object['deleted'] = false;
                $object['changedAt'] = date("Y-m-d H:i:s");
                $object->save();
            }
        }
        return $this->redirect(['../site/trash']);
    }
}
