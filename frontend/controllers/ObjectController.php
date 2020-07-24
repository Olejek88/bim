<?php

namespace frontend\controllers;

use common\components\MainFunctions;
use common\models\DistrictCoordinates;
use common\models\Measure;
use common\models\MeasureChannel;
use common\models\MeasureType;
use common\models\Objects;
use common\models\ObjectSubType;
use common\models\ObjectType;
use common\models\Parameter;
use common\models\ParameterType;
use dosamigos\leaflet\layers\Marker;
use dosamigos\leaflet\types\Icon;
use dosamigos\leaflet\types\LatLng;
use dosamigos\leaflet\types\Point;
use frontend\models\ActionRegisterSearch;
use frontend\models\AlarmSearch;
use frontend\models\EventSearch;
use frontend\models\MeasureChannelSearch;
use frontend\models\ObjectsSearch;
use frontend\models\ParameterSearch;
use Yii;
use yii\base\InvalidConfigException;
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
        if (isset($_POST['editableAttribute'])) {
            $model = Objects::find()
                ->where(['_id' => $_POST['editableKey']])
                ->one();
            if ($_POST['editableAttribute'] == 'title') {
                $model['title'] = $_POST['Objects'][$_POST['editableIndex']]['title'];
            }
            if ($model->save())
                return json_encode('success');
            return json_encode('failed');
        }

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
     * Lists all districts.
     * @return mixed
     * @throws InvalidConfigException
     */
    public function actionDistricts()
    {
        if (isset($_POST['editableAttribute'])) {
            $model = Objects::find()
                ->where(['_id' => $_POST['editableKey']])
                ->one();
            if ($_POST['editableAttribute'] == 'title') {
                $model['title'] = $_POST['Objects'][$_POST['editableIndex']]['title'];
            }
            if ($model->save())
                return json_encode('success');
            return json_encode('failed');
        }

        $searchModel = new ObjectsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 50;
        $dataProvider->query->andWhere(['objectTypeUuid' => ObjectType::SUB_DISTRICT]);

        return $this->render('districts', [
            'dataProvider' => $dataProvider
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
                            'expanded' => true,
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
                            $links .= Html::a('<span class="fa fa-database"></span>&nbsp',
                                ['/parameter/list', 'uuid' => $object['uuid']],
                                [
                                    'title' => Yii::t('app', 'Параметры объекта'),
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modalParameter',
                                ]
                            );
                            $links .= Html::a('<span class="fa fa-warning"></span>&nbsp',
                                ['/alarm/list', 'uuid' => $object['uuid']],
                                [
                                    'title' => Yii::t('app', 'Предупреждения по объекту'),
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modalParameter',
                                ]
                            );
                            $links .= Html::a('<span class="fa fa-list-ul"></span>&nbsp',
                                ['/attribute/list', 'uuid' => $object['uuid']],
                                [
                                    'title' => Yii::t('app', 'Атрибуты объекта'),
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modalParameter',
                                ]
                            );
                            $links .= Html::a('<span class="fa fa-th"></span>&nbsp',
                                ['/object/dashboard', 'uuid' => $object['uuid']]
                            );
                            $type = '';
                            if ($object->source) $type .= '<span class="fa fa-plug" title="Поставщик"></span>&nbsp;&nbsp;';
                            $type .= '<span class="fa fa-thermometer" title="Учет тепла"></span>&nbsp';
                            if ($object->water) $type .= '<span class="fa fa-tint" title="Учет ХВС"></span>&nbsp';
                            if ($object->electricity) $type .= '<span class="fa fa-bolt" title="Учет электроэнергии"></span>';

                            $fullTree['children'][$childIdx]['children'][$childIdx2]['children'][$childIdx3]['children'][$childIdx4]['children'][] = [
                                'title' => $object->getFullTitle(),
                                'type' => 'object',
                                'links' => $links,
                                'expanded' => true,
                                'measure_type' => $type,
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
                                $links = Html::a('<span class="fa fa-database"></span>&nbsp',
                                    ['/parameter/list', 'uuid' => $channel['uuid']],
                                    [
                                        'title' => Yii::t('app', 'Параметры канала'),
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modalParameter',
                                    ]
                                );
                                $links .= Html::a('<span class="fa fa-th"></span>&nbsp',
                                    ['/measure-channel/dashboard', 'uuid' => $channel['uuid']]
                                );

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
            if (true) {
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

    /**
     * @return mixed
     */
    public
    function actionDashboard()
    {
        $request = Yii::$app->request;
        $objectUuid = $request->get('uuid');
        if ($objectUuid) {
            /** @var Objects $object */
            $object = Objects::find()->where(['uuid' => $objectUuid])->limit(1)->one();
            if ($object) {
                $searchModel = new ParameterSearch();
                $parameters = $searchModel->search(Yii::$app->request->queryParams);
                $parameters->pagination->pageSize = 20;
                $parameters->query->andWhere(['entityUuid' => $object->uuid]);

                $searchModel = new EventSearch();
                $events = $searchModel->search(Yii::$app->request->queryParams);
                $events->pagination->pageSize = 0;
                $events->query->andWhere(['objectUuid' => $object->uuid]);

                $searchModel = new MeasureChannelSearch();
                $channels = $searchModel->search(Yii::$app->request->queryParams);
                $channels->pagination->pageSize = 0;
                $channels->query->andWhere(['objectUuid' => $object->uuid]);
                $channels->query->andWhere(['<>', 'status', MeasureChannel::STATUS_OFF]);

                $searchModel = new AlarmSearch();
                $alarms = $searchModel->search(Yii::$app->request->queryParams);
                $alarms->pagination->pageSize = 0;
                $alarms->query->andWhere(['entityUuid' => $object->uuid]);

                $searchModel = new ActionRegisterSearch();
                $registers = $searchModel->search(Yii::$app->request->queryParams);
                $registers->pagination->pageSize = 0;
                $registers->query->andWhere(['entityUuid' => $object->uuid]);

                $position = new LatLng(['lat' => $object["latitude"], 'lng' => $object["longitude"]]);
                $marker = new Marker(['latLng' => $position, 'popupContent' => '<b>'
                    . htmlspecialchars($object->getFullTitle()) . '</b><br/>'
                    . htmlspecialchars($object->objectSubType->title) . '<br/>'
                ]);
                $objectIcon = new Icon([
                    'iconUrl' => '/images/marker-icon.png',
                    'iconSize' => new Point(['x' => 28, 'y' => 43]),
                    'iconAnchor' => new Point (['x' => 14, 'y' => 43]),
                    'popupAnchor' => new Point (['x' => -3, 'y' => -76])
                ]);
                $marker->setIcon($objectIcon);
                $coordinates = new LatLng(['lat' => $object["latitude"], 'lng' => $object["longitude"]]);

                $data['month'] = [];
                $channel_uuid = [];
                $measureChannels = MeasureChannel::find()->where(['objectUuid' => $object['uuid']])
                    ->andWhere(['deleted' => 0])
                    ->andWhere(['type' => MeasureType::MEASURE_TYPE_MONTH])
                    ->all();
                foreach ($measureChannels as $measureChannel) {
                    $channel_uuid[] = $measureChannel['uuid'];
                }
                /** @var Measure[] $last_measures */
                $last_measures = Measure::find()
                    ->where(['in', 'measureChannelUuid', $channel_uuid])
                    ->orderBy('date desc')->all();
                $cnt = -1;
                $last_date = '';
                foreach ($last_measures as $measure) {
                    if ($measure['date'] != $last_date)
                        $last_date = $measure['date'];
                    $data['month'][$cnt]['date'] = $measure['date'];
                    if ($measure->measureChannel->measureTypeUuid == MeasureType::HEAT_CONSUMED)
                        $data['month'][$cnt]['heat'] = $measure['value'];
                    if ($measure->measureChannel->measureTypeUuid == MeasureType::COLD_WATER)
                        $data['month'][$cnt]['water'] = $measure['value'];
                    if ($measure->measureChannel->measureTypeUuid == MeasureType::ENERGY)
                        $data['month'][$cnt]['power'] = $measure['value'];
                }

                return $this->render('dashboard', [
                    'object' => $object,
                    'coordinates' => $coordinates,
                    'marker' => $marker,
                    'parameters' => $parameters,
                    'events' => $events,
                    'channels' => $channels,
                    'alarms' => $alarms,
                    'registers' => $registers,
                    'measures' => $data
                ]);
            }
        }
        return null;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public
    function actionPlanEdit()
    {
        if (isset($_GET["month"]))
            $month = $_GET["month"];
        else $month = '';
        if (isset($_GET["entityUuid"]) && isset($_GET["month"])) {
            $parameter = new Parameter();
            if (isset($_GET["parameter_uuid"])) {
                $parameter = Parameter::find()->where(['uuid' => $_GET["parameter_uuid"]])->one();
                if (!$parameter) {
                    $parameter = new Parameter();
                }
            }
            return $this->renderAjax('_add_plan', [
                'date' => $month,
                'entityUuid' => $_GET["entityUuid"],
                'objectUuid' => $_GET["entityUuid"],
                'parameter' => $parameter
            ]);
        }
        return null;
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
        $allObjects = Objects::find()->where(['objectTypeUuid' => ObjectType::OBJECT])->all();
        foreach ($allObjects as $object) {
            $measureChannelHeat = MeasureChannel::find()
                ->where(['objectUuid' => $object['uuid']])
                ->andWhere(['measureTypeUuid' => MeasureType::HEAT_CONSUMED])
                ->andWhere(['type' => MeasureType::MEASURE_TYPE_MONTH])
                ->one();
            for ($i = 1; $i < $month_count; $i++) {
                $sum[$i] = 0;
            }
            $objects[$count]['title'] = $object->getFullTitle();
            /*            if ($measureChannelHeat) {
                            $objects[$count]['title'] = $measureChannelHeat['uuid'];
                        }*/
            for ($month = 1; $month < $month_count; $month++) {
                $objects[$count]['plans'][$month]['plan'] = '';
                $parameter_uuid = null;
                $parameterValue = '<span class="span-plan0">n/a</span>';
                if ($measureChannelHeat) {
                    $parameter = Parameter::find()
                        ->where(['entityUuid' => $measureChannelHeat['uuid']])
                        ->andWhere(['date' => $mon_date_str2[$month]])
                        ->andWhere(['parameterTypeUuid' => ParameterType::TARGET_CONSUMPTION])
                        ->one();
                    if ($parameter) {
                        $parameterValue = "<span class='span-plan1'>" . $parameter['value'] . "</span>";
                        $parameter_uuid = $parameter['uuid'];
                    }
                    $objects[$count]['plans'][$month]['plan']
                        = Html::a($parameterValue, ['/object/plan-edit', 'month' => $mon_date_str[$month],
                        'parameter_uuid' => $parameter_uuid,
                        'entityUuid' => $measureChannelHeat['uuid']],
                        [
                            'title' => 'Редактировать',
                            'data-toggle' => 'modal',
                            'data-target' => '#modalPlan',
                        ]);
                } else {
                    $objects[$count]['plans'][$month]['plan'] = '<span class="span-plan0">-</span>';
                }
                $measureValue = '<span class="span-plan0">-</span>';
                if ($measureChannelHeat) {
                    $measure = Measure::find()
                        ->where(['measureChannelUuid' => $measureChannelHeat['uuid']])
                        ->andWhere(['date' => $mon_date_str2[$month]])
                        ->one();
                    if ($measure) {
                        $measureValue = "<span class='span-plan1'>" . $measure['value'] . "</span>";
                    }
                }
                $objects[$count]['plans'][$month]['fact'] = $measureValue;
            }
            $count++;
        }
        return $this->render('plan', [
            'objects' => $objects,
            'month_count' => $month_count,
            'dates' => $dates_title
        ]);
    }
}
