<?php

namespace frontend\controllers;

use common\components\MainFunctions;
use common\models\ActionRegister;
use common\models\Alarm;
use common\models\DistrictCoordinates;
use common\models\Event;
use common\models\LoginForm;
use common\models\Measure;
use common\models\MeasureChannel;
use common\models\MeasureType;
use common\models\Objects;
use common\models\ObjectType;
use common\models\Register;
use common\models\ServiceRegister;
use common\models\User;
use dosamigos\leaflet\controls\Layers;
use dosamigos\leaflet\layers\Marker;
use dosamigos\leaflet\layers\Polygon;
use dosamigos\leaflet\layers\TileLayer;
use dosamigos\leaflet\LeafLet;
use dosamigos\leaflet\types\Icon;
use dosamigos\leaflet\types\LatLng;
use dosamigos\leaflet\types\Point;
use Exception;
use frontend\models\SignupForm;
use koputo\leaflet\plugins\subgroup\Subgroup;
use koputo\leaflet\plugins\subgroup\SubgroupCluster;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Html;
use yii\web\Controller;

/**
 * Class SiteController
 * @package frontend\controllers
 */
class SiteController extends Controller
{
    /**
     * Behaviors
     *
     * @inheritdoc
     *
     * @return array
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['signup', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'dashboard', 'error', 'timeline', 'config', 'trash', 'stats'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Actions
     *
     * @return array
     */
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['error']);
        return $actions;
    }

    /**
     * Displays homepage.
     *
     * @return string
     * @throws Exception
     */
    public function actionIndex()
    {
        $layer = self::getLayers();
        $center = $layer['coordinates'];

        // The Tile Layer (very important)
        $tileLayer = new TileLayer([
            'urlTemplate' => 'https://api.tiles.mapbox.com/v4/mapbox.streets/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw',
            'clientOptions' => [
                'subdomains' => ['1', '2', '3', '4'],
            ],
        ]);
        $leaflet = new LeafLet([
            'center' => $center, // set the center
        ]);

        $layers = new Layers();

        $js[] = '$("#show-save").click(function(){
                        var x = document.getElementById("polygon-control");
                        var control = document.getElementById("show-save");
                        if (x.style.display === "none") {
                            x.style.display = "block";
                            control.style.display = "none"; 
                        } else {
                            x.style.display = "none";
                            control.style.display = "block";
                        }
                  });';
        $js[] .= '$("#get-button-features-polygon").click(function(e){
                       e.preventDefault();
                       let selected_features = selectfeature.getFeaturesSelected(\'polygon\');
                       let coordinates = selectfeature._ARR_latlon;
	                   console.log(coordinates);
	                   $.ajax({
                           url: "../object/new-district",
                           type: "post",
                           data: {
                               latlng: JSON.stringify(coordinates)
                           },
                           success: function (data) { 
                               $(\'#modalAdd\').modal(\'show\');
                               $(\'#modalContent\').html(data);
                           }
                       }); 
	                });
	                $("#disable-button").click(function(){
	                    selectfeature = map.selectAreaFeature.disable();
	                });
	                $("#enable-button").click(function(){
	                    selectfeature = map.selectAreaFeature.enable();
	                    selectfeature.options.color = \'#663399\' ;
	                    selectfeature.options.weight = 3 ;
	             });';
        $leaflet->setJs($js);

        $leaflet->setJs('/js/Leaflet.SelectAreaFeature.js');

        // Different layers can be added to our map using the `addLayer` function.
        $leaflet->addLayer($tileLayer);

        $subGroupPlugin = new SubgroupCluster();
        $subGroupPlugin->addSubGroup($layer['objectGroup']);
        $subGroupPlugin->addSubGroup($layer['regionGroup']);
        $subGroupPlugin->addSubGroup($layer['alarmGroup']);
        $layers->setOverlays([]);

        $layers->setName('ctrlLayer');

        $leaflet->addControl($layers);
        $layers->position = 'bottomleft';

        // install to LeafLet component
        $leaflet->plugins->install($subGroupPlugin);

        return $this->render(
            'index',
            [
                'leafLet' => $leaflet,
            ]
        );
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            Yii::$app->session->setFlash('success', 'Thank you for registration. Please check your inbox for verification email.');
            return $this->goHome();
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Dashboard
     *
     * @return string
     * @throws Exception
     */
    public function actionDashboard()
    {
        $objectsCount = Objects::find()->where(['deleted' => 0])->count();
        $objectsTypeCount = ObjectType::find()->count();
        $channelsCount = MeasureChannel::find()->where(['deleted' => 0])->count();
        $measuresCount = Measure::find()->count();
        $eventsCount = Event::find()->count();
        $layer = self::getLayers();
        // По числу в шаблоне
        $registers = ServiceRegister::find()->orderBy('_id desc')->limit(8)->all();
        return $this->render(
            'dashboard',
            [
                'layer' => $layer,
                'registers' => $registers,
                'objectsTypeCount' => $objectsTypeCount,
                'objectsCount' => $objectsCount,
                'channelsCount' => $channelsCount,
                'measuresCount' => $measuresCount,
                'eventsCount' => $eventsCount,
                'categories' => [],
                'values' => []
            ]
        );
    }

    /**
     *
     * @return string
     * @throws Exception
     */
    public function actionStats()
    {
        $today = getdate();

        $categories_month = "'" . Yii::t('app', 'Январь') . "','" .
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
        $bar .= "{ name: 'Месяцы',";
        $bar .= "data: [";
        $zero = 0;
        for ($m = 1; $m <= 12; $m++) {
            $start = sprintf("%d%02d01000000", $today['year'], $m);
            $end = sprintf("%d%02d01000000", $today['year'], $m + 1);
            $values = Measure::find()
                ->where('date>=' . $start)
                ->andWhere('date<' . $end)
                ->count();
            if ($zero > 0) {
                $bar .= ",";
            }
            $bar .= $values;
            $zero++;
        }
        $bar .= "]}";

        $values_days = '';
        $values_days .= "{ name: 'Дни',";
        $values_days .= "data: [";
        $zero = 0;
        $max_date = date('t');
        $categories_days = "";
        for ($day = 1; $day <= $max_date; $day++) {
            $categories_days .= $day . ',';
            $start = sprintf("%d%02d%02d000000", $today['year'], $today['mon'], $day);
            $end = sprintf("%d%02d%02d235959", $today['year'], $today['mon'], $day);
            $values = Measure::find()
                ->where('date>=' . $start)
                ->andWhere('date<=' . $end)
                ->count();
            if ($zero > 0) {
                $values_days .= ",";
            }
            $values_days .= $values;
            $zero++;
        }
        $categories_days = substr($categories_days, 0, -1);
        $values_days .= "]}";

        $channels = [];
        $count = 0;
        /** @var Objects[] $objects */
        $objects = Objects::find()->where(['objectTypeUuid' => ObjectType::OBJECT])->orderBy('title')->all();
        foreach ($objects as $object) {
            $channels[$count]['object'] = $object->getFullTitle();
            $channels[$count]['energy'] = MeasureChannel::find()
                ->where(['objectUuid' => $object->uuid])
                ->andWhere(['measureTypeUuid' => MeasureType::ENERGY])
                ->count();
            $channels[$count]['heat'] = MeasureChannel::find()
                ->where(['objectUuid' => $object->uuid])
                ->andWhere(['measureTypeUuid' => MeasureType::HEAT_CONSUMED])
                ->count();
            $channels[$count]['water'] = MeasureChannel::find()
                ->where(['objectUuid' => $object->uuid])
                ->andWhere(['measureTypeUuid' => MeasureType::COLD_WATER])
                ->count();
            $channels[$count]['all'] = MeasureChannel::find()
                ->where(['objectUuid' => $object->uuid])
                ->count();
            $count++;
        }

        $stats['channels'] = MeasureChannel::find()->count();
        $stats['channel_types'] = MeasureType::find()->count();
        $stats['data'] = Measure::find()->count();
        $stats['data_types'] = MeasureType::find()->count();

        $measureChannels = MeasureChannel::find()->all();
        $chan1 = [];
        $chan2 = [];
        $chan3 = [];
        foreach ($measureChannels as $measureChannel) {
            if ($measureChannel->measureTypeUuid == MeasureType::ENERGY || $measureChannel->measureTypeUuid == MeasureType::VOLTAGE) {
                $chan1[] = $measureChannel['uuid'];
            }
            if ($measureChannel->measureTypeUuid == MeasureType::HEAT_CONSUMED || $measureChannel->measureTypeUuid == MeasureType::HEAT_IN) {
                $chan2[] = $measureChannel['uuid'];
            }
            if ($measureChannel->measureTypeUuid == MeasureType::COLD_WATER || $measureChannel->measureTypeUuid == MeasureType::HOT_WATER) {
                $chan3[] = $measureChannel['uuid'];
            }
        }
        $data_by_source[0]['cnt'] = Measure::find()
            ->where(['in', 'measureChannelUuid', $chan1])
            ->count();
        $data_by_source[1]['cnt'] = Measure::find()
            ->where(['in', 'measureChannelUuid', $chan2])
            ->count();
        $data_by_source[2]['cnt'] = Measure::find()
            ->where(['in', 'measureChannelUuid', $chan3])
            ->count();
        $data_by_source[0]['title'] = 'Электроэнергия';
        $data_by_source[1]['title'] = 'Тепло';
        $data_by_source[2]['title'] = 'Вода';

        $data_by_type[0]['cnt'] = Measure::find()->joinWith('measureChannel')
            ->where(['measure_channel.type' => MeasureType::MEASURE_TYPE_CURRENT])->count();
        $data_by_type[1]['cnt'] = Measure::find()->joinWith('measureChannel')
            ->where(['measure_channel.type' => MeasureType::MEASURE_TYPE_HOURS])->count();
        $data_by_type[2]['cnt'] = Measure::find()->joinWith('measureChannel')
            ->where(['measure_channel.type' => MeasureType::MEASURE_TYPE_DAYS])->count();
        $data_by_type[0]['title'] = 'Текущие';
        $data_by_type[1]['title'] = 'Часовые';
        $data_by_type[2]['title'] = 'Дневные';

        return $this->render(
            'stats',
            [
                'categories_month' => $categories_month,
                'values_month' => $bar,
                'categories_days' => $categories_days,
                'values_days' => $values_days,
                'channels' => $channels,
                'data_by_source' => $data_by_source,
                'data_by_type' => $data_by_type,
                'stats' => $stats
            ]
        );
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            /** @var User $user */
            $user = Yii::$app->user->identity;

            $log = new ActionRegister();
            $log->userId = $user->id;
            $userIP = empty(Yii::$app->request->userIP) ? 'unknown' : Yii::$app->request->userIP;
            $log->title = 'Пользователь зашел в Систему с ' . $userIP;
            if (!$log->save()) {
                $errors = $log->errors;
                foreach ($errors as $error) {
                    Yii::error($error, "frontend/controllers/SiteController.php");
                }
            }
            return $this->goHome();
        } else {
            return $this->render(
                'login',
                [
                    'model' => $model,
                ]
            );
        }
    }

    /**
     * Action error
     *
     * @return string
     */
    public function actionError()
    {
        $exception = Yii::$app->errorHandler->exception;
        if ($exception !== null) {
            return $this->render("../site/error");
        }
        return '';
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays a timeline
     *
     * @return mixed
     */
    public function actionTimeline()
    {
        $events = [];
        if (!empty($_GET['type']) && is_numeric($_GET['type'])) {
            $type = intval($_GET['type']);
        } else {
            $type = null;
        }

        if ($type == null || $type == 1) {
            $registers = Register::find()
                ->orderBy('createdAt DESC')
                ->limit(20)
                ->all();
            foreach ($registers as $register) {
                $text = '<i class="fa fa-cogs"></i>&nbsp;
                <a class="btn btn-default btn-xs">' . $register->getFullTitle() . '</a><br/>
                <i class="fa fa-clipboard"></i>&nbsp;' . Yii::t('app', 'Изменил параметр') . ': <a class="btn btn-default btn-xs">'
                    . $register['title'] . '</a>';
                $events[] = ['date' => $register['date'], 'event' => self::formEvent($register['date'],
                    'register', 0, '', $text, $register['user']->name)];
            }
        }

        if ($type == null || $type == 2) {
            $object_events = Event::find()
                ->orderBy('createdAt DESC')
                ->limit(5)
                ->all();
            foreach ($object_events as $object_event) {
                $text = '<i class="fa fa-desktop"></i>&nbsp; ' . Yii::t('app', 'Система') . ': 
                <span class="btn btn-default btn-xs">' . $object_event->title . '</span><br/>
                <i class="fa fa-cogs"></i>&nbsp;' . Yii::t('app', 'Объект') . ': 
                    <span class="btn btn-default btn-xs">' . $object_event->object->getFullTitle() . '</span><br/>';
                $events[] = ['date' => $object_event['createdAt'], 'event' => self::formEvent($object_event['createdAt'],
                    'event', 0, '', $text, '')];
            }
        }

        if ($type == null || $type == 3) {
            $registers = ActionRegister::find()
                ->orderBy('createdAt DESC')
                ->limit(20)
                ->all();
            foreach ($registers as $register) {
                $text = '<i class="fa fa-user"></i>&nbsp;
                <a class="btn btn-default btn-xs">' . $register['title'] . '</a><br/>
                <i class="fa fa-clipboard"></i>&nbsp;' . Yii::t('app', 'Изменил сущность') . ': <a class="btn btn-default btn-xs">'
                    . $register->getEntityName() . '</a>';
                $events[] = ['date' => $register['createdAt'], 'event' => self::formEvent($register['createdAt'],
                    'register', 0, '', $text, $register['user']->username)];
            }
        }

        $sort_events = MainFunctions::array_msort($events, ['date' => SORT_DESC]);
        $today = date("j-m-Y h:i");

        return $this->render(
            'timeline',
            [
                'events' => $sort_events,
                'today_date' => $today,
                'type' => $type,
            ]
        );
    }

    /**
     * Формируем код записи о событии
     * @param $date
     * @param $type
     * @param $id
     * @param $title
     * @param $text
     * @param $user
     *
     * @return string
     */
    public static function formEvent($date, $type, $id, $title, $text, $user)
    {
        $event = '<li>';
        if ($type == 'register')
            $event .= '<i class="fa fa-cogs bg-green"></i>';
        if ($type == 'attribute')
            $event .= '<i class="fa fa-user bg-gray"></i>';
        if ($type == 'event')
            $event .= '<i class="fa fa-link bg-maroon"></i>';

        $event .= '<div class="timeline-item">';
        $event .= '<span class="time"><i class="fa fa-clock-o"></i> ' . date("M j, Y h:i", strtotime($date)) . '</span>';
        if ($type == 'event') {
            $event .= '<span class="timeline-header" style="vertical-align: middle">' .
                Yii::t('app', 'Добавлено или изменено событие &nbsp') . " " . $title . '</span>';
        }
        if ($type == 'register') {
            $event .= '&nbsp;<span class="btn btn-primary btn-xs">' . $user . '</span>&nbsp;
                    <span class="timeline-header" style="vertical-align: middle">' .
                Yii::t('app', 'Действие в системе &nbsp;') . " " . $title . '</span>';
        }
        if ($type == 'attribute') {
            $event .= '&nbsp;<span class="btn btn-primary btn-xs">' . $user . '</span>&nbsp; 
                    <span class="timeline-header" style="vertical-align: middle">' .
                Yii::t('app', 'Изменен аттрибут &nbsp;') . " " . $title . '</span>';
        }
        $event .= '<div class="timeline-body">' . $text . '</div>';
        $event .= '</div></li>';
        return $event;
    }

    /**
     */
    public function actionConfig()
    {
        $this->enableCsrfValidation = false;

        if (isset($_POST["period"])) {
//            Settings::storeSetting(Settings::SETTING_PERIOD, $_POST["period"]);
        }
        return $this->redirect($_SERVER["HTTP_REFERER"]);
    }

    /**
     * Displays a trash can
     *
     * @return mixed
     */
    public function actionTrash()
    {
        $events = [];
        $type = null;
        $objects = Objects::find()
            ->where(['deleted' => 1])
            ->orderBy('changedAt DESC')
            ->all();
        if ($type == 2 || $type == null) {
            foreach ($objects as $object) {
                $text = Html::a("<i class='fa fa-refresh'></i>", ['../object/restore', 'uuid' => $object['uuid']]);
                $text .= '&nbsp;' . Yii::t('app', 'Объект') . ': <a class="btn btn-primary btn-xs">' . $object['title'] . '</a>';
                $events[] = ['date' => $object['changedAt'], 'event' => self::formTrashEvent($object['changedAt'],
                    'object', $text)];
            }
        }

        $sort_events = MainFunctions::array_msort($events, ['date' => SORT_DESC]);
        $today = date("j-m-Y h:i");

        return $this->render(
            'trash',
            [
                'events' => $sort_events,
                'today_date' => $today,
                'type' => $type,
            ]
        );
    }

    /**
     * Формируем код записи о событии
     * @param $date
     * @param $type
     * @param $text
     *
     * @return string
     */
    public static function formTrashEvent($date, $type, $text)
    {
        $event = '<li>';
        if ($type == 'object')
            $event .= '<i class="fa fa-home bg-aqua"></i>';

        $event .= '<div class="timeline-item">';
        $event .= '<span class="time" style="padding: 2px !important;"><i class="fa fa-clock-o"></i> ' . date("M j, Y h:i", strtotime($date)) . '</span>';
        $event .= '<span class="timeline-header" style="vertical-align: middle">' . $text . '</span>';
        $event .= '</div></li>';
        return $event;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getLayers()
    {
        $objectsGroup = new SubGroup();
        $objectsGroup->setTitle(Yii::t('app', 'Объекты'));
        $alarmGroup = new SubGroup();
        $alarmGroup->setTitle(Yii::t('app', 'Предупреждения'));
        $regionGroup = new SubGroup();
        $regionGroup->setTitle(Yii::t('app', 'Районы'));

        $alarmIcon = new Icon([
            'iconUrl' => '/images/marker_defect_m.png',
            'iconSize' => new Point(['x' => 28, 'y' => 43]),
            'iconAnchor' => new Point (['x' => 14, 'y' => 43]),
            'popupAnchor' => new Point (['x' => -3, 'y' => -76])
        ]);
        $objectIcon = new Icon([
            'iconUrl' => '/images/marker-icon.png',
            'iconSize' => new Point(['x' => 28, 'y' => 43]),
            'iconAnchor' => new Point (['x' => 14, 'y' => 43]),
            'popupAnchor' => new Point (['x' => -3, 'y' => -76])
        ]);

        /**
         * Вывод точек фиксации показаний и неисправности (measuredValue/defect -> equipment -> object)
         */
        $alarms = Alarm::find()
            ->orderBy('createdAt desc')
            ->limit(30)
            ->all();

        foreach ($alarms as $alarm) {
            if ($alarm["entityUuid"]) {
                $object = Objects::find()->where(['uuid' => $alarm["entityUuid"]])->one();
                if ($object["latitude"] > 0) {
                    $position = new LatLng(['lat' => $object["latitude"], 'lng' => $object["longitude"]]);
                    $marker = new Marker(['latLng' => $position, 'popupContent' => '<b>'
                        . $object["title"] . '</b><br/>'
                        . $alarm->getAlarmLabel() . " " . $alarm["title"]]);
                    $marker->setIcon($alarmIcon);
                    $alarmGroup->addLayer($marker);
                }
            }
        }

        $objectSelect = Objects::find()
            ->select('_id, title, latitude, longitude, objectTypeUuid, parentUuid')
            ->where(['deleted' => 0])
            ->andWhere(['objectTypeUuid' => ObjectType::OBJECT])
            ->all();

        $default_coordinates = new LatLng(['lat' => 55.54, 'lng' => 61.36]);
        $coordinates = $default_coordinates;
        foreach ($objectSelect as $object) {
            $position = new LatLng(['lat' => $object["latitude"], 'lng' => $object["longitude"]]);
            $marker = new Marker(['latLng' => $position, 'popupContent' => '<b>'
                . htmlspecialchars($object->getFullTitle()) . '</b>']);
            $marker->setIcon($objectIcon);
            $objectsGroup->addLayer($marker);
            $coordinates = new LatLng(['lat' => $object["latitude"], 'lng' => $object["longitude"]]);
            if ($coordinates->lng == $default_coordinates->lng && $coordinates->lat == $default_coordinates->lat && $object["latitude"] > 0) {
                $coordinates = new LatLng(['lat' => $object["latitude"], 'lng' => $object["longitude"]]);
            }
        }

        $districts = DistrictCoordinates::find()->all();
        /** @var DistrictCoordinates $district */
        foreach ($districts as $district) {
            $district_coordinates = json_decode($district->coordinates);
            $coordinates_latlng = [];
            foreach ($district_coordinates as $coordinate) {
                $coordinates_latlng[] = new LatLng(['lat' => $coordinate->lat, 'lng' => $coordinate->lng]);
            }
            $polygon = new Polygon(['latLngs' => $coordinates_latlng, 'popupContent' => '<b>'
                . htmlspecialchars($district->district->getFullTitle()) . '</b>']);
            $regionGroup->addLayer($polygon);
        }

        $layer['objectGroup'] = $objectsGroup;
        $layer['alarmGroup'] = $alarmGroup;
        $layer['regionGroup'] = $regionGroup;
        $layer['coordinates'] = $coordinates;
        return $layer;
    }

}
