<?php

namespace frontend\controllers;

use common\components\MainFunctions;
use common\models\DistrictCoordinates;
use common\models\Event;
use common\models\Measure;
use common\models\MeasureChannel;
use common\models\MeasureType;
use common\models\ObjectDistrict;
use common\models\Objects;
use common\models\ObjectSubType;
use common\models\ObjectType;
use common\models\Parameter;
use common\models\ParameterType;
use dosamigos\leaflet\layers\Marker;
use dosamigos\leaflet\types\Icon;
use dosamigos\leaflet\types\LatLng;
use dosamigos\leaflet\types\Point;
use Exception;
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
            $selected_node = rtrim($selected_node, 'k');
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
                            if ($object->source) $type .= '<span class="fa fa-plug" title="Поставщик"></span>&nbsp;';
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
                                //$value = '';
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
        if (isset($_POST['editableAttribute'])) {
            $model = Parameter::find()
                ->where(['_id' => $_POST['editableKey']])
                ->one();
            if ($_POST['editableAttribute'] == 'value') {
                $model['value'] = $_POST['Parameter'][$_POST['editableIndex']]['value'];
            }
            $model->save();
            return json_encode('');
        }

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
                $parameters->query->andWhere(['parameter_type.type' => 1]);

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
                $channel_id = [];
                $measureChannels = MeasureChannel::find()->where(['objectUuid' => $object['uuid']])
                    ->andWhere(['deleted' => 0])
                    ->andWhere(['type' => MeasureType::MEASURE_TYPE_MONTH])
                    ->all();
                foreach ($measureChannels as $measureChannel) {
                    $channel_id[] = $measureChannel['_id'];
                }
                /** @var Measure[] $last_measures */
                $last_measures = Measure::find()
                    ->where(['in', 'measureChannelId', $channel_id])
                    ->orderBy('date desc')->all();

                $last_date = '';
                for ($cnt = 0; $cnt < 7; $cnt++) {
                    $data['month'][$cnt]['date'] = "-";
                    $data['month'][$cnt]['heat'] = "-";
                    $data['month'][$cnt]['water'] = "-";
                    $data['month'][$cnt]['power'] = "-";
                }
                $cnt = -1;
                foreach ($last_measures as $measure) {
                    if ($measure['date'] != $last_date) {
                        $last_date = $measure['date'];
                        $cnt++;
                        if ($cnt > 6) break;
                        $data['month'][$cnt]['date'] = $measure['date'];
                    }
                    if ($measure->measureChannel->measureTypeUuid == MeasureType::HEAT_CONSUMED)
                        $data['month'][$cnt]['heat'] = $measure['value'];
                    if ($measure->measureChannel->measureTypeUuid == MeasureType::COLD_WATER)
                        $data['month'][$cnt]['water'] = $measure['value'];
                    if ($measure->measureChannel->measureTypeUuid == MeasureType::ENERGY)
                        $data['month'][$cnt]['power'] = $measure['value'];
                }

                $time = localtime(time(), true);
                $year[0] = $time['tm_year'] + 1900;
                $year[1] = $year[0] - 1;
                $year[2] = $year[0] - 2;
                $date[0] = sprintf("%d0101000000", $year[0]);
                $date[10] = sprintf("%d1231000000", $year[0]);
                $date[1] = sprintf("%d0101000000", $year[1]);
                $date[11] = sprintf("%d1231000000", $year[1]);
                $date[2] = sprintf("%d0101000000", $year[2]);
                $date[12] = sprintf("%d1231000000", $year[2]);

                $heatGroup = [];
                for ($c = 0; $c <= 5; $c++) {
                    for ($m = 1; $m <= 12; $m++) {
                        $heatGroup[$c][$m] = 0;
                    }
                }

                $measureChannelHeat = MeasureChannel::find()
                    ->where(['objectUuid' => $object['uuid']])
                    ->andWhere(['deleted' => 0])
                    ->andWhere(['type' => MeasureType::MEASURE_TYPE_MONTH])
                    ->andWhere(['measureTypeUuid' => MeasureType::HEAT_CONSUMED])
                    ->limit(1)
                    ->one();
                $objectCity = Objects::find()->where(['objectTypeUuid' => ObjectType::CITY])->andWhere(['deleted' => 0])->limit(1)->one();
                $measureChannelMonth = null;
                if ($objectCity) {
                    $measureChannelMonth = MeasureChannel::getChannel($objectCity['uuid'], MeasureType::TEMPERATURE_AIR, MeasureType::MEASURE_TYPE_MONTH);
                }
                if ($measureChannelMonth) {
                    $temperatures = Measure::find()
                        ->where(['measureChannelId' => $measureChannelMonth['_id']])
                        ->orderBy('date DESC')
                        ->asArray()
                        ->all();
                    foreach ($temperatures as $measure) {
                        $measure_month = intval(date("m", strtotime($measure['date'])));
                        $measure_year = date("Y", strtotime($measure['date']));
                        if ($measure_year == $year[0]) {
                            $heatGroup[5][$measure_month] = $measure['value'];
                        }
                        if ($measure_year == $year[1]) {
                            $heatGroup[3][$measure_month] = $measure['value'];
                        }
                        if ($measure_year == $year[2]) {
                            $heatGroup[1][$measure_month] = $measure['value'];
                        }
                    }
                }

                if ($measureChannelHeat) {
                    $measures = Measure::find()->where(['measureChannelId' => $measureChannelHeat['_id']])->all();
                    foreach ($measures as $measure) {
                        $measure_month = intval(date("m", strtotime($measure['date'])));
                        $measure_year = date("Y", strtotime($measure['date']));
                        if ($measure_year == $year[0]) {
                            $heatGroup[4][$measure_month] = $measure['value'];
                        }
                        if ($measure_year == $year[1]) {
                            $heatGroup[2][$measure_month] = $measure['value'];
                        }
                        if ($measure_year == $year[2]) {
                            $heatGroup[0][$measure_month] = $measure['value'];
                        }
                    }
                }

                $years[0]['title'] = 'Тепло ' . $year[2];
                $years[1]['title'] = 'Тнв ' . $year[2];
                $years[2]['title'] = 'Тепло ' . $year[1];
                $years[3]['title'] = 'Тнв ' . $year[1];
                $years[4]['title'] = 'Тепло ' . $year[0];
                $years[5]['title'] = 'Тнв ' . $year[0];

                $first = 0;
                $categories = "'" . Yii::t('app', 'Январь') . "','" .
                    Yii::t('app', 'Февраль') . "','" .
                    Yii::t('app', 'Март') . "','" .
                    Yii::t('app', 'Апрель') . "','" .
                    Yii::t('app', 'Май') . "','" .
                    Yii::t('app', 'Июнь') . "','" .
                    Yii::t('app', 'Июль') . "','" .
                    Yii::t('app', 'Август') . "','" .
                    Yii::t('app', 'Сентябрь') . "','" .
                    Yii::t('app', 'Октябрь') . "','" .
                    Yii::t('app', 'Ноябрь') . "','" .
                    Yii::t('app', 'Декабрь') . "'";

                $bar = '';
                for ($c = 0; $c <= 5; $c++) {
                    if ($first > 0) {
                        $bar .= "," . PHP_EOL;
                    }

                    $bar .= "{ name: '" . $years[$c]['title'] . "',";
                    $bar .= "data: [";
                    $zero = 0;
                    for ($m = 1; $m <= 12; $m++) {
                        if (isset($heatGroup[$c][$m])) {
                            if ($zero > 0) {
                                $bar .= ",";
                            }
                            $bar .= $heatGroup[$c][$m];
                            $zero++;
                        }
                    }
                    $bar .= "]}";
                    $first++;
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
                    'measures' => $data,
                    'categories' => $categories,
                    'values' => $bar
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
        if (isset($_GET["entityUuid"]) && isset($_GET["month"])) {
            $event = Event::find()->where(['uuid' => $_GET["entityUuid"]])->one();
            if ($event) {
                return $this->renderAjax('_edit_plan', [
                    'event' => $event
                ]);
            }
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
                        = Html::a($parameterValue, ['/object/parameter-edit', 'month' => $mon_date_str[$month],
                        'parameter_uuid' => $parameter_uuid,
                        'parameterTypeUuid' => ParameterType::TARGET_CONSUMPTION,
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

    /**
     * @return string
     */
    public function actionTable()
    {
        setlocale(LC_TIME, 'ru_RU.UTF-8', 'Russian_Russia', 'Russian');
        $objects = [];
        $mon_date_str = [];
        $mon_date_str2[] = [];
        $mon_date_str3[] = [];
        $month_count = 1;
        $dates_title = [];
        $dates = date("Ym01 00:00:00", time() - 3600 * 24 * 31);
        while ($month_count < 3) {
            $mon_date[$month_count] = strtotime($dates);
            $mon_date_str[$month_count] = strftime("%Y%m01000000", $mon_date[$month_count]);
            $mon_date_str2[$month_count] = strftime("%Y%m01000000", $mon_date[$month_count]);
            $dates_title[$month_count] = strftime("%h", $mon_date[$month_count]);
            $localtime = localtime($mon_date[$month_count], true);
            $mon = $localtime['tm_mon'];
            $year = $localtime['tm_year'];
            $mon--;
            if ($mon == 1) {
                $mon = 12;
                $year--;
            }
            $dates = sprintf("%d-%02d-01 00:00:00", $year + 1900, $mon + 1);
            $mon_date_str3[$month_count] = strftime("%Y%m01000000", strtotime($dates));
            $month_count++;
        }
        $count = 0;
        /** @var Objects[] $allObjects */
        $districtUuid = Yii::$app->request->get('district');
        if ($districtUuid) {
            $district = Objects::find()->where(['uuid' => $districtUuid])->limit(1)->one();
            if ($district) {
                $allObjects = $district->getObjectsByDistrict(100);
            }
        } else {
            $allObjects = Objects::find()->where(['objectTypeUuid' => ObjectType::OBJECT])->all();
        }
        foreach ($allObjects as $object) {
            $measureChannelHeat = MeasureChannel::getChannel($object['uuid'], MeasureType::HEAT_CONSUMED, MeasureType::MEASURE_TYPE_MONTH);
            $measureChannelWater = MeasureChannel::getChannel($object['uuid'], MeasureType::COLD_WATER, MeasureType::MEASURE_TYPE_MONTH);
            $measureChannelEnergy = MeasureChannel::getChannel($object['uuid'], MeasureType::ENERGY, MeasureType::MEASURE_TYPE_MONTH);

            for ($i = 1; $i < $month_count; $i++) {
                $sumHeat[$i] = 0;
                $sumWater[$i] = 0;
                $sumPower[$i] = 0;
            }
            $objects[$count]['title'] = Html::a($object->getFullTitle(), ['object/month', 'uuid' => $object['uuid']]);
            $objects[$count]['region'] = $object->getSubDistrict();
            $objects[$count]['type'] = $object->objectSubType->title;
            $objects[$count]['efficiency'] = $object->getParameter(ParameterType::ENERGY_EFFICIENCY);
            $objects[$count]['equipment'] = $object->getParameter(ParameterType::POWER_EQUIPMENT);
            $objects[$count]['water'] = $object->water;
            $objects[$count]['electricity'] = $object->electricity;

            $objects[$count]['heat'] = '-';
            $objects[$count]['water'] = '-';
            $objects[$count]['electricity'] = '-';
            if ($measureChannelHeat) {
                $objects[$count]['heat'] =
                    Html::a($object->getCurrent(MeasureType::HEAT_CONSUMED, true),
                        ['measure-channel/dashboard', 'uuid' => $measureChannelHeat['uuid']]);
            }
            if ($measureChannelWater) {
                $objects[$count]['water'] =
                    Html::a($object->getCurrent(MeasureType::COLD_WATER, true),
                        ['measure-channel/dashboard', 'uuid' => $measureChannelWater['uuid']]);
            }
            if ($measureChannelEnergy) {
                $objects[$count]['electricity'] =
                    Html::a($object->getCurrent(MeasureType::ENERGY, true),
                        ['measure-channel/dashboard', 'uuid' => $measureChannelEnergy['uuid']]);
            }

            for ($month = 1; $month < $month_count; $month++) {
                $objects[$count]['plans'][$month]['plan_heat'] = '<span class="span-plan0">-</span>';
                $objects[$count]['plans'][$month]['plan_water'] = '<span class="span-plan0">-</span>';
                $objects[$count]['plans'][$month]['plan_electricity'] = '<span class="span-plan0">-</span>';
                if ($measureChannelHeat) {
                    $parameterHeat = $measureChannelHeat->getParameter(ParameterType::TARGET_CONSUMPTION, $mon_date_str2[$month]);
                    if ($parameterHeat) {
                        $objects[$count]['plans'][$month]['plan_heat'] = $parameterHeat['value'];
                    }
                }
                $objects[$count]['plans'][$month]['plan_water'] = '<span class="span-plan0">-</span>';
                if ($measureChannelWater) {
                    $parameterWater = $measureChannelWater->getParameter(ParameterType::TARGET_CONSUMPTION, $mon_date_str2[$month]);
                    if ($parameterWater) {
                        $objects[$count]['plans'][$month]['plan_water'] = $parameterWater['value'];
                    }
                }
                $objects[$count]['plans'][$month]['plan_electricity'] = '<span class="span-plan0">-</span>';
                if ($measureChannelEnergy) {
                    $parameterEnergy = $measureChannelEnergy->getParameter(ParameterType::TARGET_CONSUMPTION, $mon_date_str2[$month]);
                    if ($parameterEnergy)
                        $objects[$count]['plans'][$month]['plan_electricity'] = $parameterEnergy['value'];
                }
                $objects[$count]['plans'][$month]['fact_heat'] = '<span class="span-plan0">-</span>';
                $objects[$count]['plans'][$month]['fact_water'] = '<span class="span-plan0">-</span>';
                $objects[$count]['plans'][$month]['fact_electricity'] = '<span class="span-plan0">-</span>';
                if ($measureChannelHeat) {
                    $measure = Measure::find()
                        ->where(['measureChannelUuid' => $measureChannelHeat['uuid']])
                        ->andWhere(['date' => $mon_date_str2[$month]])
                        ->one();
                    if ($measure) {
                        $objects[$count]['plans'][$month]['fact_heat'] = "<span class='span-plan1'>" . $measure['value'] . "</span>";
                    }
                }
                if ($measureChannelWater) {
                    $measure = Measure::find()
                        ->where(['measureChannelUuid' => $measureChannelWater['uuid']])
                        ->andWhere(['date' => $mon_date_str2[$month]])
                        ->one();
                    if ($measure) {
                        $objects[$count]['plans'][$month]['fact_water'] = "<span class='span-plan1'>" . $measure['value'] . "</span>";
                    }
                }
                if ($measureChannelEnergy) {
                    $measure = Measure::find()
                        ->where(['measureChannelUuid' => $measureChannelEnergy['uuid']])
                        ->andWhere(['date' => $mon_date_str2[$month]])
                        ->one();
                    if ($measure) {
                        $objects[$count]['plans'][$month]['fact_electricity'] = "<span class='span-plan1'>" . $measure['value'] . "</span>";
                    }
                }
            }
            $count++;
        }
        $districts = Objects::find()->where(['objectTypeUuid' => ObjectType::SUB_DISTRICT])->all();
        $districts = ArrayHelper::map($districts, 'uuid', 'title');

        return $this->render('table', [
            'objects' => $objects,
            'districts' => $districts,
            'month_count' => $month_count,
            'dates' => $dates_title
        ]);
    }

    /**
     * Проверяем наличие параметров коэффициентов для объекта, и если их нет - создаем их
     *
     * @param $objectUuid
     * @throws \Exception
     */
    public static
    function createConsumptionCoefficients($objectUuid)
    {
        $channelHeat = MeasureChannel::getChannel($objectUuid, MeasureType::HEAT_CONSUMED, MeasureType::MEASURE_TYPE_MONTH);
        $channelWater = MeasureChannel::getChannel($objectUuid, MeasureType::COLD_WATER, MeasureType::MEASURE_TYPE_MONTH);
        $channelEnergy = MeasureChannel::getChannel($objectUuid, MeasureType::ENERGY, MeasureType::MEASURE_TYPE_MONTH);
        if ($channelHeat) {
            $parameter = Parameter::find()
                ->where(['entityUuid' => $channelHeat['uuid']])
                ->andWhere(['parameterTypeUuid' => ParameterType::CONSUMPTION_COEFFICIENT])
                ->limit(1)
                ->one();
            if (!$parameter) {
                for ($month = 1; $month <= 12; $month++) {
                    $parameter = new Parameter();
                    $parameter->uuid = MainFunctions::GUID();
                    $parameter->entityUuid = $channelHeat['uuid'];
                    $parameter->parameterTypeUuid = ParameterType::CONSUMPTION_COEFFICIENT;
                    $parameter->date = sprintf("2000%02d01000000", $month);
                    switch ($month) {
                        case 1:
                            $parameter->value = 1;
                            break;
                        case 2:
                            $parameter->value = 1;
                            break;
                        case 3:
                            $parameter->value = 0.9;
                            break;
                        case 4:
                            $parameter->value = 0.7;
                            break;
                        case 5:
                            $parameter->value = 0.4;
                            break;
                        case 10:
                            $parameter->value = 0.4;
                            break;
                        case 11:
                            $parameter->value = 0.9;
                            break;
                        case 12:
                            $parameter->value = 1;
                            break;
                        default:
                            $parameter->value = 0;
                    }
                    $parameter->save();
                }
            }
        }
        if ($channelWater) {
            $parameter = Parameter::find()
                ->where(['entityUuid' => $channelWater['uuid']])
                ->andWhere(['parameterTypeUuid' => ParameterType::CONSUMPTION_COEFFICIENT])
                ->limit(1)
                ->one();
            if (!$parameter) {
                for ($month = 1; $month <= 12; $month++) {
                    $parameter = new Parameter();
                    $parameter->uuid = MainFunctions::GUID();
                    $parameter->entityUuid = $channelWater['uuid'];
                    $parameter->parameterTypeUuid = ParameterType::CONSUMPTION_COEFFICIENT;
                    $parameter->date = sprintf("2000%02d01000000", $month);
                    switch ($month) {
                        case 5:
                            $parameter->value = 0.9;
                            break;
                        case 6:
                            $parameter->value = 0.9;
                            break;
                        case 7:
                            $parameter->value = 0.9;
                            break;
                        case 8:
                            $parameter->value = 0.9;
                            break;
                        default:
                            $parameter->value = 1;
                    }
                    $parameter->save();
                }
            }
        }
        if ($channelEnergy) {
            $parameter = Parameter::find()
                ->where(['entityUuid' => $channelEnergy['uuid']])
                ->andWhere(['parameterTypeUuid' => ParameterType::CONSUMPTION_COEFFICIENT])
                ->limit(1)
                ->one();
            if (!$parameter) {
                for ($month = 1; $month <= 12; $month++) {
                    $parameter = new Parameter();
                    $parameter->uuid = MainFunctions::GUID();
                    $parameter->entityUuid = $channelEnergy['uuid'];
                    $parameter->parameterTypeUuid = ParameterType::CONSUMPTION_COEFFICIENT;
                    $parameter->date = sprintf("2000%02d01000000", $month);
                    switch ($month) {
                        case 1:
                            $parameter->value = 1;
                            break;
                        case 2:
                            $parameter->value = 1;
                            break;
                        case 3:
                            $parameter->value = 1;
                            break;
                        case 4:
                            $parameter->value = 0.9;
                            break;
                        case 11:
                            $parameter->value = 0.9;
                            break;
                        case 12:
                            $parameter->value = 1;
                            break;
                        default:
                            $parameter->value = 0.8;
                    }
                    $parameter->save();
                }
            }
        }
    }

    /**
     * @return string
     */
    public
    function actionBase()
    {
        setlocale(LC_TIME, 'ru_RU.UTF-8', 'Russian_Russia', 'Russian');
        $objects = [];
        $dates_title = [];
        $mon_date_str = [];
        $mon_date_str2[] = [];
        $mon_date_str3[] = [];

        $month_count = 1;
        $dates = date("19700101 00:00:00", time());
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
            $measureChannelHeat = MeasureChannel::getChannel($object['uuid'], MeasureType::HEAT_CONSUMED, MeasureType::MEASURE_TYPE_MONTH);
            for ($i = 1; $i < $month_count; $i++) {
                $sum[$i] = 0;
            }
            $objects[$count]['title'] = $object->getFullTitle();
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

    /**
     * @return string
     */
    public function actionTarget()
    {
        setlocale(LC_TIME, 'ru_RU.UTF-8', 'Russian_Russia', 'Russian');
        $type = Yii::$app->request->getQueryParam('type');
        if (!isset($type)) {
            $type = 'heat';
        }
        $analyse = Yii::$app->request->getQueryParam('analyse');

        $objects = [];
        $dates_title = [];
        $mon_date_str = [];
        $mon_date_str2[] = [];
        $mon_date_str3[] = [];

        $month_count = 1;
        $dates = date("Y0101 00:00:00", time());
        while ($month_count <= 12) {
            $mon_date[$month_count] = strtotime($dates);
            $mon_date_str[$month_count] = strftime("2000%m01000000", $mon_date[$month_count]);
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
            /** @var MeasureChannel $measureChannel */
            $measureChannel = null;
            $parameter_uuid = null;
            $baseConsumption = 0;
            if ($type == 'heat') {
                $measureChannel = MeasureChannel::getChannel($object['uuid'], MeasureType::HEAT_CONSUMED, MeasureType::MEASURE_TYPE_MONTH);
                $objects[$count]['base'] = "n/a";
                if ($measureChannel) {
                    $baseConsumption = $measureChannel->getParameter(ParameterType::BASE_CONSUMPTION, date("Y0101000000", time()));
                    if ($baseConsumption) {
                        $objects[$count]['base'] = $baseConsumption['value'];
                        $parameter_uuid = $baseConsumption['uuid'];
                    }
                    $objects[$count]['base'] = Html::a($objects[$count]['base'],
                        ['/object/parameter-edit', 'parameter_uuid' => $parameter_uuid, 'entityUuid' => $measureChannel['uuid'],
                            'month' => date("Y0101000000", time()), 'parameterTypeUuid' => ParameterType::BASE_CONSUMPTION],
                        ['title' => 'Редактировать', 'data-toggle' => 'modal', 'data-target' => '#modalEditParameter']);
                }
            }
            if ($type == 'water') {
                $measureChannel = MeasureChannel::getChannel($object['uuid'], MeasureType::COLD_WATER, MeasureType::MEASURE_TYPE_MONTH);
                $objects[$count]['base'] = "n/a";
                if ($measureChannel) {
                    $baseConsumption = $measureChannel->getParameter(ParameterType::BASE_CONSUMPTION, Parameter::DEFAULT_DATE);
                    if ($baseConsumption) {
                        $objects[$count]['base'] = $baseConsumption['value'];
                    }
                    $objects[$count]['base'] = Html::a($objects[$count]['base'],
                        ['/object/parameter-edit', 'parameter_uuid' => $parameter_uuid, 'entityUuid' => $measureChannel['uuid'],
                            'month' => date("Y0101000000", time()), 'parameterTypeUuid' => ParameterType::BASE_CONSUMPTION],
                        ['title' => 'Редактировать', 'data-toggle' => 'modal', 'data-target' => '#modalEditParameter']);
                }
            }

            if ($type == 'energy') {
                $measureChannel = MeasureChannel::getChannel($object['uuid'], MeasureType::ENERGY, MeasureType::MEASURE_TYPE_MONTH);
                $objects[$count]['base'] = "n/a";
                if ($measureChannel) {
                    $baseConsumption = $measureChannel->getParameter(ParameterType::BASE_CONSUMPTION, Parameter::DEFAULT_DATE);
                    if ($baseConsumption) {
                        $objects[$count]['base'] = $baseConsumption['value'];
                    }
                    $objects[$count]['base'] = Html::a($objects[$count]['base'],
                        ['/object/parameter-edit', 'parameter_uuid' => $parameter_uuid, 'entityUuid' => $measureChannel['uuid'],
                            'month' => date("Y0101000000", time()), 'parameterTypeUuid' => ParameterType::BASE_CONSUMPTION],
                        ['title' => 'Редактировать', 'data-toggle' => 'modal', 'data-target' => '#modalEditParameter']);
                }
            }
            for ($i = 1; $i < $month_count; $i++) {
                $sum[$i] = 0;
            }
            $objects[$count]['title'] = $object->getFullTitle();

            for ($month = 1; $month < $month_count; $month++) {
                $objects[$count]['plans'][$month]['coefficient'] = '-';
                $objects[$count]['plans'][$month]['consumption'] = '-';
                $objects[$count]['plans'][$month]['diff'] = "-";
                $objects[$count]['plans'][$month]['fact'] = "-";
                $parameter_uuid = null;
                $parameterValue = '<span class="span-plan0">n/a</span>';
                if ($measureChannel) {
                    $parameter = $measureChannel->getParameter(ParameterType::CONSUMPTION_COEFFICIENT, $mon_date_str[$month]);
                    if ($parameter) {
                        $parameterValue = "<span class='span-plan1'>" . $parameter['value'] . "</span>";
                        $parameter_uuid = $parameter['uuid'];
                        $objects[$count]['plans'][$month]['consumption'] = '-';
                        if ($baseConsumption) {
                            $objects[$count]['plans'][$month]['consumption'] = $parameter['value'] * $baseConsumption['value'];
                        }
                    }
                    $objects[$count]['plans'][$month]['coefficient']
                        = Html::a($parameterValue, ['/object/parameter-edit', 'month' => $mon_date_str[$month], 'parameterTypeUuid' => ParameterType::CONSUMPTION_COEFFICIENT,
                        'parameter_uuid' => $parameter_uuid, 'entityUuid' => $measureChannel['uuid']],
                        ['title' => 'Редактировать', 'data-toggle' => 'modal', 'data-target' => '#modalEditParameter']);

                    $measure = Measure::find()
                        ->where(['measureChannelUuid' => $measureChannel['uuid']])
                        ->andWhere(['date' => $mon_date_str2[$month]])
                        ->one();
                    if ($measure) {
                        $objects[$count]['plans'][$month]['fact'] = "<span class='span-plan1'>" . $measure['value'] . "</span>";
                        if ($baseConsumption) {
                            $targetConsumption = $parameter['value'] * $baseConsumption['value'];
                            if ($targetConsumption > 0) {
                                $objects[$count]['plans'][$month]['diff'] =
                                    number_format(($targetConsumption - $measure['value']) * 100 / $targetConsumption, 2);
                                if ($objects[$count]['plans'][$month]['diff'] > 0) {
                                    $objects[$count]['plans'][$month]['diff'] =
                                        '<span class="progress" style="margin-top: 0 !important;"><span class="critical2">' . $objects[$count]['plans'][$month]['diff'] . '</span></span>';
                                } else {
                                    $objects[$count]['plans'][$month]['diff'] =
                                        '<span class="progress" style="margin-top: 0 !important;"><span class="critical3">' . $objects[$count]['plans'][$month]['diff'] . '</span></span>';
                                }
                            }
                        }
                    }
                }
            }
            $count++;
        }
        if ($analyse) {
            return $this->render('target-analyse', [
                'objects' => $objects,
                'month_count' => $month_count,
                'dates' => $dates_title
            ]);
        }
        return $this->render('target', [
            'objects' => $objects,
            'month_count' => $month_count,
            'dates' => $dates_title
        ]);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public
    function actionParameterEdit()
    {
        if (isset($_GET["entityUuid"])) {
            $parameter = null;
            if (isset($_GET["parameter_uuid"])) {
                $parameter = Parameter::find()->where(['uuid' => $_GET["parameter_uuid"]])->one();
            }
            if (!$parameter) {
                $parameter = new Parameter();
            }
            return $this->renderAjax('../parameter/_edit', [
                'parameter' => $parameter,
                'parameterTypeUuid' => $_GET["parameterTypeUuid"],
                'entityUuid' => $_GET["entityUuid"],
                'date' => $_GET["month"]
            ]);
        }
        return null;
    }

    /**
     * @return mixed
     */
    public
    function actionMonth()
    {
        $request = Yii::$app->request;
        $objectUuid = $request->get('uuid');
        if ($objectUuid) {
            /** @var Objects $object */
            $object = Objects::find()->where(['uuid' => $objectUuid])->limit(1)->one();
            if ($object) {
                $data = [];
                $measures_heat = [];
                $measures_water = [];
                $measures_energy = [];

                $measureChannelHeat = MeasureChannel::getChannel($object['uuid'], MeasureType::HEAT_CONSUMED, MeasureType::MEASURE_TYPE_MONTH);
                $measureChannelWater = MeasureChannel::getChannel($object['uuid'], MeasureType::COLD_WATER, MeasureType::MEASURE_TYPE_MONTH);
                $measureChannelEnergy = MeasureChannel::getChannel($object['uuid'], MeasureType::ENERGY, MeasureType::MEASURE_TYPE_MONTH);
                if ($measureChannelHeat) {
                    $measures_heat = Measure::find()
                        ->where(['measureChannelUuid' => $measureChannelHeat['uuid']])
                        ->orderBy('date DESC')
                        ->limit(24)
                        ->all();
                }
                if ($measureChannelWater) {
                    $measures_water = Measure::find()
                        ->where(['measureChannelUuid' => $measureChannelWater['uuid']])
                        ->orderBy('date DESC')
                        ->limit(24)
                        ->all();
                }
                if ($measureChannelEnergy) {
                    $measures_energy = Measure::find()
                        ->where(['measureChannelUuid' => $measureChannelEnergy['uuid']])
                        ->orderBy('date DESC')
                        ->limit(24)
                        ->all();
                }

                $mon_date_str = [];
                $values['heat'] = "{ name: 'Тепло', data: [";
                $categories['heat'] = "";
                $values['water'] = "{ name: 'Вода', data: [";
                $categories['water'] = "";
                $values['energy'] = "{ name: 'Электро', data: [";
                $categories['energy'] = "";

                $zero_heat = 0;
                $zero_water = 0;
                $zero_energy = 0;

                $localtime = localtime(time(), true);
                $mon = $localtime['tm_mon'];
                $year = $localtime['tm_year'];
                $month_count = 0;
                while ($month_count < 12) {
                    $mon_date_str[$month_count] = sprintf("%d-%02d-01 00:00:00", $year + 1900, $mon);
                    $data[$month_count]['date'] = Html::a(sprintf("%d-%02d-01", $year + 1900, $mon),
                        ['/object/days', 'year' => $year, 'month' => $mon, 'uuid' => $objectUuid]);
                    $data[$month_count]['heat'] = "-";
                    $data[$month_count]['water'] = "-";
                    $data[$month_count]['energy'] = "-";
                    foreach ($measures_heat as $measure) {
                        if ($measure['date'] == $mon_date_str[$month_count]) {
                            $data[$month_count]['heat'] = $measure['value'];
                            $categories['heat'] .= '\'' . date("Y-m", strtotime($measure->date)) . '\',';
                            if ($zero_heat > 0) {
                                $values['heat'] .= ",";
                            }
                            $values['heat'] .= $measure->value;
                            $zero_heat++;
                            break;
                        }
                    }
                    foreach ($measures_water as $measure) {
                        if ($measure['date'] == $mon_date_str[$month_count]) {
                            $data[$month_count]['water'] = $measure['value'];
                            $categories['water'] .= '\'' . date("d H:i", strtotime($measure->date)) . '\',';
                            if ($zero_water > 0) {
                                $values['water'] .= ",";
                            }
                            $values['water'] .= $measure->value;
                            $zero_water++;
                            break;
                        }
                    }
                    foreach ($measures_energy as $measure) {
                        if ($measure['date'] == $mon_date_str[$month_count]) {
                            $data[$month_count]['energy'] = $measure['value'];
                            $categories['energy'] .= '\'' . date("d H:i", strtotime($measure->date)) . '\',';
                            if ($zero_energy > 0) {
                                $values['energy'] .= ",";
                            }
                            $values['energy'] .= $measure->value;
                            $zero_energy++;
                            break;
                        }
                    }
                    $mon--;
                    if ($mon < 1) {
                        $mon = 12;
                        $year--;
                    }
                    $month_count++;
                }
                $categories['heat'] = substr($categories['heat'], 0, -1);
                $categories['water'] = substr($categories['water'], 0, -1);
                $categories['energy'] = substr($categories['energy'], 0, -1);
                $values['heat'] .= "]}";
                $values['water'] .= "]}";
                $values['energy'] .= "]}";

                return $this->render('bar-month', [
                    'object' => $object,
                    'measures' => $data,
                    'categories' => $categories,
                    'measureChannelHeat' => $measureChannelHeat,
                    'measureChannelWater' => $measureChannelWater,
                    'measureChannelEnergy' => $measureChannelEnergy,
                    'values' => $values
                ]);
            }
        }
        return null;
    }

    /**
     * @return mixed
     */
    public
    function actionDays()
    {
        $request = Yii::$app->request;
        $objectUuid = $request->get('uuid');
        $year = $request->get('year');
        $month = $request->get('month');
        if ($objectUuid && $month && $year) {
            /** @var Objects $object */
            $object = Objects::find()->where(['uuid' => $objectUuid])->limit(1)->one();
            if ($object) {
                $data = [];
                $measures_heat = [];
                $measures_water = [];
                $measures_energy = [];

                $measureChannelHeat = MeasureChannel::getChannel($object['uuid'], MeasureType::HEAT_CONSUMED, MeasureType::MEASURE_TYPE_DAYS);
                $measureChannelWater = MeasureChannel::getChannel($object['uuid'], MeasureType::COLD_WATER, MeasureType::MEASURE_TYPE_DAYS);
                $measureChannelEnergy = MeasureChannel::getChannel($object['uuid'], MeasureType::ENERGY, MeasureType::MEASURE_TYPE_DAYS);

                $startDate = sprintf("%d%02d01000000", $year + 1900, $month);
                $endDate = sprintf("%d%02d31235959", $year + 1900, $month);

                if ($measureChannelHeat) {
                    $measures_heat = Measure::find()
                        ->where(['measureChannelUuid' => $measureChannelHeat['uuid']])
                        ->andWhere(['>=', 'date', $startDate])
                        ->andWhere(['<', 'date', $endDate])
                        ->orderBy('date DESC')
                        ->all();
                }
                if ($measureChannelWater) {
                    $measures_water = Measure::find()
                        ->where(['measureChannelUuid' => $measureChannelWater['uuid']])
                        ->orderBy('date DESC')
                        ->andWhere(['>=', 'date', $startDate])
                        ->andWhere(['<', 'date', $endDate])
                        ->all();
                }
                if ($measureChannelEnergy) {
                    $measures_energy = Measure::find()
                        ->where(['measureChannelUuid' => $measureChannelEnergy['uuid']])
                        ->orderBy('date DESC')
                        ->andWhere(['>=', 'date', $startDate])
                        ->andWhere(['<', 'date', $endDate])
                        ->all();
                }

                $mon_date_str = [];
                $values['heat'] = "{ name: 'Тепло', data: [";
                $categories['heat'] = "";
                $values['water'] = "{ name: 'Вода', data: [";
                $categories['water'] = "";
                $values['energy'] = "{ name: 'Электро', data: [";
                $categories['energy'] = "";

                $zero_heat = 0;
                $zero_water = 0;
                $zero_energy = 0;

                $time = strtotime($startDate);
                $day = date('t', $time);
                $cnt = 0;
                while ($day) {
                    $mon_date_str[$cnt] = sprintf("%d-%02d-%02d 00:00:00", $year + 1900, $month, $day);
                    $data[$cnt]['date'] = sprintf("%d-%02d-%02d", $year + 1900, $month, $day);
                    $data[$cnt]['heat'] = "-";
                    $data[$cnt]['water'] = "-";
                    $data[$cnt]['energy'] = "-";
                    foreach ($measures_heat as $measure) {
                        if ($measure['date'] == $mon_date_str[$cnt]) {
                            $data[$cnt]['heat'] = $measure['value'];
                            $categories['heat'] .= '\'' . date("Y-m", strtotime($measure->date)) . '\',';
                            if ($zero_heat > 0) {
                                $values['heat'] .= ",";
                            }
                            $values['heat'] .= $measure->value;
                            $zero_heat++;
                            break;
                        }
                    }
                    foreach ($measures_water as $measure) {
                        if ($measure['date'] == $mon_date_str[$cnt]) {
                            $data[$cnt]['water'] = $measure['value'];
                            $categories['water'] .= '\'' . date("d H:i", strtotime($measure->date)) . '\',';
                            if ($zero_water > 0) {
                                $values['water'] .= ",";
                            }
                            $values['water'] .= $measure->value;
                            $zero_water++;
                            break;
                        }
                    }
                    foreach ($measures_energy as $measure) {
                        if ($measure['date'] == $mon_date_str[$cnt]) {
                            $data[$cnt]['energy'] = $measure['value'];
                            $categories['energy'] .= '\'' . date("d H:i", strtotime($measure->date)) . '\',';
                            if ($zero_energy > 0) {
                                $values['energy'] .= ",";
                            }
                            $values['energy'] .= $measure->value;
                            $zero_energy++;
                            break;
                        }
                    }
                    $day--;
                    $cnt++;
                }
                $categories['heat'] = substr($categories['heat'], 0, -1);
                $categories['water'] = substr($categories['water'], 0, -1);
                $categories['energy'] = substr($categories['energy'], 0, -1);
                $values['heat'] .= "]}";
                $values['water'] .= "]}";
                $values['energy'] .= "]}";

                return $this->render('bar-days', [
                    'object' => $object,
                    'measures' => $data,
                    'categories' => $categories,
                    'measureChannelHeat' => $measureChannelHeat,
                    'measureChannelWater' => $measureChannelWater,
                    'measureChannelEnergy' => $measureChannelEnergy,
                    'values' => $values
                ]);
            }
        }
        return null;
    }

    /**
     * Метод не факт что будет нужен, так как все меняется регулярно
     * @return mixed
     * @throws \Exception
     */
    public
    function checkObjectDistricts()
    {
        $districts = Objects::find()->where(['objectTypeUuid' => ObjectType::SUB_DISTRICT])->all();
        foreach ($districts as $district) {
            $objects = $district->getObjectsByDistrict(100);
            foreach ($objects as $object) {
                $objectDistrict = ObjectDistrict::find()
                    ->where(['districtUuid' => $district['uuid']])
                    ->andWhere(['objectUuid' => $object['uuid']])
                    ->one();
                if (!$objectDistrict) {
                    $objectDistrict = new ObjectDistrict();
                    $objectDistrict->uuid = MainFunctions::GUID();
                    $objectDistrict->districtUuid = $district['uuid'];
                    $objectDistrict->objectUuid = $object['uuid'];
                    $objectDistrict->save();
                }
            }
        }
    }

    /**
     * @return string
     */
    public function actionEfficiency()
    {
        setlocale(LC_TIME, 'ru_RU.UTF-8', 'Russian_Russia', 'Russian');
        $objects = [];
        /** @var Objects[] $allObjects */
        $districtUuid = Yii::$app->request->get('district');
        if ($districtUuid) {
            $district = Objects::find()->where(['uuid' => $districtUuid])->limit(1)->one();
            if ($district) {
                $allObjects = $district->getObjectsByDistrict(100);
            }
        } else {
            $allObjects = Objects::find()->where(['objectTypeUuid' => ObjectType::OBJECT])->all();
        }
        $time = localtime(time(), true);
        $year[0] = $time['tm_year'] + 1900;
        $year[1] = $year[0] - 1;
        $year[2] = $year[0] - 2;
        $date[0] = sprintf("%d0101000000", $year[0]);
        $date[10] = sprintf("%d1231000000", $year[0]);
        $date[1] = sprintf("%d0101000000", $year[1]);
        $date[11] = sprintf("%d1231000000", $year[1]);
        $date[2] = sprintf("%d0101000000", $year[2]);
        $date[12] = sprintf("%d1231000000", $year[2]);
        $count = 0;
        foreach ($allObjects as $object) {
            $measureChannelHeat = MeasureChannel::getChannel($object['uuid'], MeasureType::HEAT_CONSUMED, MeasureType::MEASURE_TYPE_MONTH);
            //$measureChannelWater = MeasureChannel::getChannel($object['uuid'], MeasureType::COLD_WATER, MeasureType::MEASURE_TYPE_MONTH);
            //$measureChannelEnergy = MeasureChannel::getChannel($object['uuid'], MeasureType::ENERGY, MeasureType::MEASURE_TYPE_MONTH);

            $objects[$count]['title'] = Html::a($object->getFullTitle(), ['object/month', 'uuid' => $object['uuid']]);
            $objects[$count]['region'] = $object->getSubDistrict();
            $objects[$count]['type'] = $object->objectSubType->title;
            $objects[$count]['equipment'] = $object->getParameter(ParameterType::POWER_EQUIPMENT);
            $objects[$count]['water'] = $object->water;
            $objects[$count]['electricity'] = $object->electricity;
            $objects[$count]['square'] = $object->getParameter(ParameterType::SQUARE);
            $objects[$count]['wall_width'] = $object->getParameter(ParameterType::WALL_WIDTH);
            $objects[$count]['cnt_wall'] = $object->getParameter(ParameterType::KNT_HEAT_CONDUCT);
            $objects[$count]['cnt_roof'] = $object->getParameter(ParameterType::KNT_ROOF);
            $objects[$count]['cnt_window'] = $object->getParameter(ParameterType::KNT_WINDOW);

            $objects[$count]['volume'] = $object->getParameter(ParameterType::VOLUME);
            $objects[$count]['stage'] = $object->getParameter(ParameterType::STAGE_COUNT);
            $objects[$count]['workers'] = $object->getParameter(ParameterType::PERSONAL_CNT);

            $objects[$count]['efficiency'][0] = $object->getParameter(ParameterType::ENERGY_EFFICIENCY, $date[0]);
            $objects[$count]['efficiency'][1] = $object->getParameter(ParameterType::ENERGY_EFFICIENCY, $date[1]);
            $objects[$count]['efficiency'][2] = $object->getParameter(ParameterType::ENERGY_EFFICIENCY, $date[2]);

            $objects[$count]['base_heat'] = '-';
            $objects[$count]['base_water'] = '-';
            $objects[$count]['base_energy'] = '-';
            $objects[$count]['consumption'][0] = '';
            $objects[$count]['consumption'][1] = '';
            $objects[$count]['consumption'][2] = '';
            if ($measureChannelHeat) {
                $base = $measureChannelHeat->getParameter(ParameterType::BASE_CONSUMPTION, Parameter::DEFAULT_DATE);
                $parameter_uuid = null;
                $value = '-';
                if ($base) {
                    $parameter_uuid = $base['uuid'];
                    $value = $base['value'];
                }
                $objects[$count]['base_heat'] = Html::a($value,
                    ['/object/parameter-edit', 'parameter_uuid' => $parameter_uuid, 'entityUuid' => $measureChannelHeat['uuid'],
                        'month' => "20000101000000", 'parameterTypeUuid' => ParameterType::BASE_CONSUMPTION],
                    ['title' => 'Редактировать', 'data-toggle' => 'modal', 'data-target' => '#modalEditParameter']);

                $measure = Measure::find()
                    ->where(['measureChannelUuid' => $measureChannelHeat['uuid']])
                    ->andWhere(['>=', 'date', $date[0]])
                    ->andWhere(['<=', 'date', $date[10]])
                    ->sum('value');
                if ($measure && $objects[$count]['square']) {
                    $objects[$count]['consumption'][0] = number_format($measure / $objects[$count]['square'], 4);
                    $objects[$count]['efficiency'][0] =
                        MainFunctions::getEfficiency($measure, $measureChannelHeat, $objects[$count]['square'], 1);
                }
                $measure = Measure::find()
                    ->where(['measureChannelUuid' => $measureChannelHeat['uuid']])
                    ->andWhere(['>=', 'date', $date[1]])
                    ->andWhere(['<=', 'date', $date[11]])
                    ->sum('value');
                if ($measure && $objects[$count]['square']) {
                    $objects[$count]['consumption'][1] = number_format($measure / $objects[$count]['square'], 4);
                    $objects[$count]['efficiency'][1] =
                        MainFunctions::getEfficiency($measure, $measureChannelHeat, $objects[$count]['square'], 1);
                }
                $measure = Measure::find()
                    ->where(['measureChannelUuid' => $measureChannelHeat['uuid']])
                    ->andWhere(['>=', 'date', $date[2]])
                    ->andWhere(['<=', 'date', $date[12]])
                    ->sum('value');
                if ($measure && $objects[$count]['square']) {
                    $objects[$count]['consumption'][2] = number_format($measure / $objects[$count]['square'], 4);
                    $objects[$count]['efficiency'][2] =
                        MainFunctions::getEfficiency($measure, $measureChannelHeat, $objects[$count]['square'], 1);
                }
            }
            $count++;
        }
        $districts = Objects::find()->where(['objectTypeUuid' => ObjectType::SUB_DISTRICT])->all();
        $districts = ArrayHelper::map($districts, 'uuid', 'title');

        return $this->render('efficiency', [
            'objects' => $objects,
            'districts' => $districts,
            'year' => $year
        ]);
    }

    /**
     * @return string
     */
    public function actionPredictive()
    {
        setlocale(LC_TIME, 'ru_RU.UTF-8', 'Russian_Russia', 'Russian');
        $objects = [];
        /** @var Objects[] $allObjects */
        $districtUuid = Yii::$app->request->get('district');
        if ($districtUuid) {
            $district = Objects::find()->where(['uuid' => $districtUuid])->limit(1)->one();
            if ($district) {
                $allObjects = $district->getObjectsByDistrict(100);
            }
        } else {
            $allObjects = Objects::find()->where(['objectTypeUuid' => ObjectType::OBJECT])->all();
        }
        $time = localtime(time(), true);
        $year[0] = $time['tm_year'] + 1900;
        $year[1] = $year[0] - 1;
        $year[2] = $year[0] - 2;
        $year[3] = $year[0] + 1;
        $year[4] = $year[0] + 2;
        $date[0] = sprintf("%d0101000000", $year[0]);
        $date[10] = sprintf("%d1231000000", $year[0]);
        $date[1] = sprintf("%d0101000000", $year[1]);
        $date[11] = sprintf("%d1231000000", $year[1]);
        $date[2] = sprintf("%d0101000000", $year[2]);
        $date[12] = sprintf("%d1231000000", $year[2]);
        $date[3] = sprintf("%d0101000000", $year[3]);
        $date[13] = sprintf("%d1231000000", $year[3]);
        $date[4] = sprintf("%d0101000000", $year[4]);
        $date[14] = sprintf("%d1231000000", $year[4]);

        $count = 0;
        foreach ($allObjects as $object) {
            $measureChannelHeat = MeasureChannel::getChannel($object['uuid'], MeasureType::HEAT_CONSUMED, MeasureType::MEASURE_TYPE_MONTH);
            //$measureChannelWater = MeasureChannel::getChannel($object['uuid'], MeasureType::COLD_WATER, MeasureType::MEASURE_TYPE_MONTH);
            //$measureChannelEnergy = MeasureChannel::getChannel($object['uuid'], MeasureType::ENERGY, MeasureType::MEASURE_TYPE_MONTH);

            $objects[$count]['title'] = Html::a($object->getFullTitle(), ['object/month', 'uuid' => $object['uuid']]);
            $objects[$count]['links'] = Html::a('<span class="fa fa-database"></span>&nbsp;',
                ['/parameter/list', 'uuid' => $object['uuid']],
                ['title' => 'Параметры', 'data-toggle' => 'modal', 'data-target' => '#modalParameters']);
            $objects[$count]['links'] .= Html::a('<span class="fa fa-list"></span>',
                ['/event/list', 'objectUuid' => $object['uuid']],
                ['title' => 'Мероприятия', 'data-toggle' => 'modal', 'data-target' => '#modalEvents']);

            $objects[$count]['square'] = $object->getParameter(ParameterType::SQUARE);
            $objects[$count]['stage'] = $object->getParameter(ParameterType::STAGE_COUNT);
            $objects[$count]['consumption'][0] = '';
            $objects[$count]['consumption'][1] = '';
            $objects[$count]['consumption'][2] = '';

            $objects[$count]['prediction'][0] = 0;
            $objects[$count]['prediction'][1] = 0;
            $objects[$count]['prediction'][2] = 0;

            $objects[$count]['events'][0] = '';
            $objects[$count]['events'][1] = '';
            $objects[$count]['events'][2] = '';

            $objects[$count]['base_heat'] = '-';
            $objects[$count]['base_water'] = '-';
            $objects[$count]['base_energy'] = '-';
            if ($measureChannelHeat) {
                $base = $measureChannelHeat->getParameter(ParameterType::BASE_CONSUMPTION, Parameter::DEFAULT_DATE);
                $parameter_uuid = null;
                $value = '-';
                if ($base) {
                    $parameter_uuid = $base['uuid'];
                    $value = $base['value'];
                }
                $objects[$count]['base_heat'] = Html::a($value,
                    ['/object/parameter-edit', 'parameter_uuid' => $parameter_uuid, 'entityUuid' => $measureChannelHeat['uuid'],
                        'month' => "20000101000000", 'parameterTypeUuid' => ParameterType::BASE_CONSUMPTION],
                    ['title' => 'Редактировать', 'data-toggle' => 'modal', 'data-target' => '#modalEditParameter']);

                $average_cnt = 0;
                $currentYearCons = 0;
                $measure = Measure::find()
                    ->where(['measureChannelUuid' => $measureChannelHeat['uuid']])
                    ->andWhere(['>=', 'date', $date[0]])
                    ->andWhere(['<=', 'date', $date[10]])
                    ->sum('value');
                if ($measure) {
                    $objects[$count]['consumption'][0] = number_format($measure, 2);
                    $currentYearCons = 1;
                } else {
                    $objects[$count]['consumption'][0] = 0;
                }
                $measure = Measure::find()
                    ->where(['measureChannelUuid' => $measureChannelHeat['uuid']])
                    ->andWhere(['>=', 'date', $date[1]])
                    ->andWhere(['<=', 'date', $date[11]])
                    ->sum('value');
                if ($measure) {
                    $objects[$count]['consumption'][1] = number_format($measure, 2);
                    $average_cnt++;
                } else {
                    $objects[$count]['consumption'][1] = 0;
                }
                $measure = Measure::find()
                    ->where(['measureChannelUuid' => $measureChannelHeat['uuid']])
                    ->andWhere(['>=', 'date', $date[2]])
                    ->andWhere(['<=', 'date', $date[12]])
                    ->sum('value');
                if ($measure) {
                    $objects[$count]['consumption'][2] = number_format($measure, 2);
                    $average_cnt++;
                } else {
                    $objects[$count]['consumption'][2] = 0;
                }

                // дальнейшее имеет смысл только если есть реальные цифры
                if ($average_cnt > 0 || $currentYearCons) {
                    $events = Event::find()
                        ->where(['objectUuid' => $object['uuid']])
                        ->andWhere('date > CURDATE()')
                        ->andWhere(['<=', 'date', $date[14]])
                        ->orderBy('date')
                        ->all();
                    // суммарный коэффициент канала
                    $sumParameters = Parameter::find()
                        ->where(['parameterTypeUuid' => ParameterType::CONSUMPTION_COEFFICIENT])
                        ->andWhere(['entityUuid' => $measureChannelHeat['uuid']])
                        ->andWhere(['>=', 'date', "20000101000000"])
                        ->andWhere(['<=', 'date', "20001231000000"])
                        ->sum('value');
                    $sumParametersRest = Parameter::find()
                        ->where(['parameterTypeUuid' => ParameterType::CONSUMPTION_COEFFICIENT])
                        ->andWhere(['entityUuid' => $measureChannelHeat['uuid']])
                        ->andWhere(['>=', 'date', strftime("2000%m01000000", time())])
                        ->sum('value');
                    $kntRest = 1;
                    if ($sumParameters) {
                        $kntRest = $sumParametersRest / $sumParameters;
                    }
                    $average = 0;
                    if ($currentYearCons) {
                        // обратный коэффициент прогноза до конца года
                        $average = $objects[$count]['consumption'][0] * (1 / (1 - $kntRest));
                        $average_cnt++;
                    }
                    // average это по сути среднее за три года
                    if ($average_cnt > 0) {
                        $average += ($objects[$count]['consumption'][2] + $objects[$count]['consumption'][1]);
                        $average /= $average_cnt;
                    }
                    $eventsImpact[0] = 0;
                    $eventsImpact[1] = 0;
                    $eventsImpact[2] = 0;
                    $eventsCount[0] = 0;
                    $eventsCount[1] = 0;
                    $eventsCount[2] = 0;

                    // если нет запланированных событий за период, то тоже нет смыла считать
                    if (count($events)) {
                        foreach ($events as $event) {
                            $eventImpact = $event['cnt_coverage'] * $event->eventType->cnt_effect;
                            $objects[$count]['prediction'][1] = $average * (1 - $eventImpact);
                            $objects[$count]['prediction'][2] = $average * (1 - $eventImpact);

                            // дата события приходится на этот год
                            if (strtotime($event['date']) >= strtotime($date[0]) && strtotime($event['date']) <= strtotime($date[10])) {
                                $eventsImpact[0] += $event['cnt_coverage'] * $event->eventType->cnt_effect * ObjectController::getMonthCoefficient(date('n', strtotime($event['date'])));
                                $eventsImpact[1] += $event['cnt_coverage'] * $event->eventType->cnt_effect;
                                $eventsImpact[2] += $event['cnt_coverage'] * $event->eventType->cnt_effect;
                                $eventsCount[0]++;
                            }
                            // дата события приходится на следующий год
                            if (strtotime($event['date']) >= strtotime($date[3]) && strtotime($event['date']) <= strtotime($date[13])) {
                                $eventsImpact[1] += $event['cnt_coverage'] * $event->eventType->cnt_effect * ObjectController::getMonthCoefficient(date('n', strtotime($event['date'])));
                                $eventsImpact[2] += $event['cnt_coverage'] * $event->eventType->cnt_effect;
                                $eventsCount[1]++;
                            }
                            // дата события приходится на последний год
                            if (strtotime($event['date']) >= strtotime($date[4]) && strtotime($event['date']) <= strtotime($date[14])) {
                                $eventsImpact[2] += $event['cnt_coverage'] * $event->eventType->cnt_effect * ObjectController::getMonthCoefficient(date('n', strtotime($event['date'])));
                                $eventsCount[2]++;
                            }
                        }
                    }
                    if ($objects[$count]['consumption'][0] > 0) {
                        $objects[$count]['prediction'][0] = number_format($objects[$count]['consumption'][0]
                            + $kntRest * $objects[$count]['consumption'][0] * (1 - $eventsImpact[0]), 2, ".", "");
                    } else {
                        $objects[$count]['prediction'][0] = number_format($average * (1 - $eventsImpact[0]), 2, ".", "");
                    }
                    $objects[$count]['prediction'][1] = number_format($average * (1 - $eventsImpact[1]), 2, ".", "");
                    $objects[$count]['prediction'][2] = number_format($average * (1 - $eventsImpact[2]), 2, ".", "");
                    $objects[$count]['events'][0] = number_format($eventsImpact[0], 4) . ' (' . Html::a($eventsCount[0],
                            ['/event/list', 'objectUuid' => $object['uuid'], 'dateStart' => $date[0], 'dateEnd' => $date[10]],
                            ['title' => 'События', 'data-toggle' => 'modal', 'data-target' => '#modalEvents']) . ')';
                    $objects[$count]['events'][1] = number_format($eventsImpact[1], 4) . ' (' . Html::a($eventsCount[1],
                            ['/event/list', 'objectUuid' => $object['uuid'], 'dateStart' => $date[3], 'dateEnd' => $date[13]],
                            ['title' => 'События', 'data-toggle' => 'modal', 'data-target' => '#modalEvents']) . ')';
                    $objects[$count]['events'][2] = number_format($eventsImpact[2], 4) . ' (' . Html::a($eventsCount[2],
                            ['/event/list', 'objectUuid' => $object['uuid'], 'dateStart' => $date[4], 'dateEnd' => $date[14]],
                            ['title' => 'События', 'data-toggle' => 'modal', 'data-target' => '#modalEvents']) . ')';
                }
            }
            $count++;
        }
        $districts = Objects::find()->where(['objectTypeUuid' => ObjectType::SUB_DISTRICT])->all();
        $districts = ArrayHelper::map($districts, 'uuid', 'title');

        return $this->render('predictive', [
            'objects' => $objects,
            'districts' => $districts,
            'year' => $year
        ]);
    }

    public static function getMonthCoefficient($month)
    {
        switch ($month) {
            case 1:
                return 5.3 / 6.3;
            case 2:
                return 4.3 / 6.3;
            case 3:
                return 3.4 / 6.3;
            case 4:
                return 2.7 / 6.3;
            case 5:
                return 2.3 / 6.3;
            case 6:
                return 2.3 / 6.3;
            case 7:
                return 2.3 / 6.3;
            case 8:
                return 2.3 / 6.3;
            case 9:
                return 2.3 / 6.3;
            case 10:
                return 1.9 / 6.3;
            case 11:
                return 1 / 6.3;
            default:
                return 0;
        }
    }

    /**
     * Dashboard
     *
     * @return string
     * @throws Exception
     */
    public function actionReport()
    {
        $this->enableCsrfValidation = false;
        $objects = Objects::find()->where(['objectTypeUuid' => ObjectType::OBJECT])
            ->andWhere(['deleted' => 0])->all();
        $measure_types = MeasureType::find()->all();
        $parameter_types = ParameterType::find()->where('type>0')->all();
        $data[] = [];
        $data[0]['parameter_title'] = [];
        $data[0]['measure_title'] = [];
        $data[0]['title'] = 'нет объектов';
        $object_count = 0;
        $parameter_count = 0;
        $measure_count = 0;
        if (isset($_POST['forms'])) {
            foreach ($parameter_types as $parameter_type) {
                if (isset($_POST['parameter' . $parameter_type['_id']])) {
                    $data[0]['parameter_title'][] = $parameter_type['title'];
                    $parameter_count++;
                }
            }
            foreach ($measure_types as $measure_type) {
                if (isset($_POST['measure' . $measure_type['_id']])) {
                    $data[0]['measure_title'][] = $measure_type['title'];
                    $measure_count++;
                }
            }

            foreach ($objects as $object) {
                if (isset($_POST['object' . $object['_id']])) {
                    $data[$object_count]['title'] = $object->getFullTitle();
                    $data[$object_count]['parameters'] = [];
                    $data[$object_count]['measures'] = [];
                    foreach ($parameter_types as $parameter_type) {
                        if (isset($_POST['parameter' . $parameter_type['_id']])) {
                            $data[$object_count]['parameters'][] = $object->getParameter($parameter_type['uuid']);
                        }
                    }
                    foreach ($measure_types as $measure_type) {
                        if (isset($_POST['measure' . $measure_type['_id']])) {
                            $data[$object_count]['measures'][$measure_count] = '';
                            $measureChannel = MeasureChannel::find()
                                ->where(['objectUuid' => $object['uuid']])
                                ->andWhere(['measureTypeUuid' => $measure_type['uuid']])
                                ->limit(1)
                                ->one();
                            if ($measureChannel) {
                                $data[$object_count]['measures'][] = $measureChannel->getLastMeasure();
                            }
                        }
                    }
                    $object_count++;
                }
            }

            return $this->render(
                'report_custom',
                ['data' => $data,
                    'count' => 1 + $parameter_count + $measure_count
                ]
            );
        }

        return $this->render(
            'report',
            ['objects' => $objects,
                'measure_types' => $measure_types,
                'parameter_types' => $parameter_types
            ]
        );
    }

    /**
     * @param $measureChannelUuid
     * @return string|null
     */
    public function actionTemperature($measureChannelUuid)
    {
        $time = localtime(time(), true);
        $year[0] = $time['tm_year'] + 1900;
        $year[1] = $year[0] - 1;
        $year[2] = $year[0] - 2;
        $date[0] = sprintf("%d0101000000", $year[0]);
        $date[10] = sprintf("%d1231000000", $year[0]);
        $date[1] = sprintf("%d0101000000", $year[1]);
        $date[11] = sprintf("%d1231000000", $year[1]);
        $date[2] = sprintf("%d0101000000", $year[2]);
        $date[12] = sprintf("%d1231000000", $year[2]);

        $heatGroup = [];
        for ($c = 0; $c <= 5; $c++) {
            for ($m = 1; $m <= 12; $m++) {
                $heatGroup[$c][$m] = 0;
            }
        }

        $object = Objects::find()->where(['objectTypeUuid' => ObjectType::CITY])->andWhere(['deleted' => 0])->limit(1)->one();
        if (!$object) return null;

        $measureChannelMonth = MeasureChannel::getChannel($object['uuid'], MeasureType::TEMPERATURE_AIR, MeasureType::MEASURE_TYPE_MONTH);
        if (!$measureChannelMonth) return null;

        $temperatures = Measure::find()
            ->where(['measureChannelUuid' => $measureChannelMonth['uuid']])
            ->orderBy('date DESC')
            ->asArray()
            ->all();
        foreach ($temperatures as $measure) {
            $measure_month = intval(date("m", strtotime($measure['date'])));
            $measure_year = date("Y", strtotime($measure['date']));
            if ($measure_year == $year[0]) {
                $heatGroup[5][$measure_month] = $measure['value'];
            }
            if ($measure_year == $year[1]) {
                $heatGroup[3][$measure_month] = $measure['value'];
            }
            if ($measure_year == $year[2]) {
                $heatGroup[1][$measure_month] = $measure['value'];
            }
        }

        $measures = Measure::find()->where(['measureChannelUuid' => $measureChannelUuid])->all();
        foreach ($measures as $measure) {
            $measure_month = intval(date("m", strtotime($measure['date'])));
            $measure_year = date("Y", strtotime($measure['date']));
            if ($measure_year == $year[0]) {
                $heatGroup[4][$measure_month] = $measure['value'];
            }
            if ($measure_year == $year[1]) {
                $heatGroup[2][$measure_month] = $measure['value'];
            }
            if ($measure_year == $year[2]) {
                $heatGroup[0][$measure_month] = $measure['value'];
            }
        }

        $years[0]['title'] = $year[2];
        $years[1]['title'] = $year[2];
        $years[2]['title'] = $year[1];
        $years[3]['title'] = $year[1];
        $years[4]['title'] = $year[0];
        $years[5]['title'] = $year[0];

        $first = 0;
        $categories = "'" . Yii::t('app', 'Январь') . "','" .
            Yii::t('app', 'Февраль') . "','" .
            Yii::t('app', 'Март') . "','" .
            Yii::t('app', 'Апрель') . "','" .
            Yii::t('app', 'Май') . "','" .
            Yii::t('app', 'Июнь') . "','" .
            Yii::t('app', 'Июль') . "','" .
            Yii::t('app', 'Август') . "','" .
            Yii::t('app', 'Сентябрь') . "','" .
            Yii::t('app', 'Октябрь') . "','" .
            Yii::t('app', 'Ноябрь') . "','" .
            Yii::t('app', 'Декабрь') . "'";

        $bar = '';
        for ($c = 0; $c <= 5; $c++) {
            if ($first > 0) {
                $bar .= "," . PHP_EOL;
            }

            $bar .= "{ name: '" . $years[$c]['title'] . "',";
            $bar .= "data: [";
            $zero = 0;
            for ($m = 1; $m <= 12; $m++) {
                if (isset($heatGroup[$c][$m])) {
                    if ($zero > 0) {
                        $bar .= ",";
                    }
                    $bar .= $heatGroup[$c][$m];
                    $zero++;
                }
            }
            $bar .= "]}";
            $first++;
        }
        return $this->render(
            'temperatures',
            ['categories' => $categories,
                'values' => $bar]
        );
    }
}
