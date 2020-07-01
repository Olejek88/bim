<?php

namespace frontend\controllers;

use common\components\MainFunctions;
use common\models\ActionRegister;
use common\models\Alarm;
use common\models\Event;
use common\models\LoginForm;
use common\models\Measure;
use common\models\MeasureChannel;
use common\models\Objects;
use common\models\ObjectType;
use common\models\Register;
use common\models\ServiceRegister;
use common\models\User;
use dosamigos\leaflet\controls\Layers;
use dosamigos\leaflet\layers\Marker;
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
                        'actions' => ['logout', 'index', 'dashboard', 'error', 'timeline', 'config', 'trash'],
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

        // Different layers can be added to our map using the `addLayer` function.
        $leaflet->addLayer($tileLayer);

        $subGroupPlugin = new SubgroupCluster();
        $subGroupPlugin->addSubGroup($layer['objectGroup']);
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

        if ($type == 2) {
        }

        $events = Event::find()
            ->orderBy('date DESC')
            ->limit(5)
            ->all();
        if ($type == null) {
            foreach ($events as $event) {
                $text = '<i class="fa fa-desktop"></i>&nbsp; ' . Yii::t('app', 'Система') . ': 
                <span class="btn btn-default btn-xs">' . $event['externalTag']['externalSystem']->title .
                    ' [' . $event['externalTag']['externalSystem']->address . ']</span><br/>
                <i class="fa fa-exclamation"></i>&nbsp' . Yii::t('app', 'Тег') . ': 
                    <span class="btn btn-primary btn-xs">' . $event['externalTag']->tag .
                    ' [' . $event['externalTag']->value . ']</span><br/>
                <i class="fa fa-cogs"></i>&nbsp;' . Yii::t('app', 'Оборудование') . ': 
                    <span class="btn btn-default btn-xs">' . $event['externalTag']['equipment']->title . '</span><br/>
                <i class="fa fa-plug"></i>&nbsp;' . Yii::t('app', 'Действие') . ': 
                    <span class="btn btn-default btn-xs">' . $event['externalTag']['actionType']->title . '</span>';
                $events[] = ['date' => $event['date'], 'event' => self::formEvent($event['date'],
                    'event', 0, '', $text, '')];
            }
        }

        $registers = Register::find()
            ->orderBy('date DESC')
            ->limit(20)
            ->all();
        if ($type == null) {
            foreach ($registers as $register) {
                $text .= '<i class="fa fa-cogs"></i>&nbsp;
                <a class="btn btn-default btn-xs">' . $register->getFullTitle() . '</a><br/>
                <i class="fa fa-clipboard"></i>&nbsp;' . Yii::t('app', 'Изменил параметр') . ': <a class="btn btn-default btn-xs">'
                    . $register['title'] . '</a>';
                $events[] = ['date' => $register['date'], 'event' => self::formEvent($register['date'],
                    'register', 0, '', $text, $register['user']->name)];
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
        if ($type == 'event')
            $event .= '<span class="timeline-header" style="vertical-align: middle">' .
                Yii::t('app', 'Добавлено или изменено событие &nbsp') . " " . $title . '</span>';

        if ($type == 'register')
            $event .= '&nbsp;<span class="btn btn-primary btn-xs">' . $user . '</span>&nbsp;
                    <span class="timeline-header" style="vertical-align: middle">' .
                Yii::t('app', 'В системе &nbsp;') . " " . $title . '</span>';

        if ($type == 'attribute')
            $event .= '&nbsp;<span class="btn btn-primary btn-xs">' . $user . '</span>&nbsp; 
                    <span class="timeline-header" style="vertical-align: middle">' .
                Yii::t('app', 'Изменен аттрибут &nbsp;') . " " . $title . '</span>';

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

        $objects = Objects::find()
            ->where(['deleted' => 1])
            ->orderBy('changedAt DESC')
            ->all();
        if ($type == 2 || $type == null) {
            foreach ($objects as $object) {
                $text = Html::a("<i class='fa fa-refresh'></i>", ['../objects/restore', 'uuid' => $object['uuid']]);
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

        $alarmIcon = new Icon([
            'iconUrl' => '/images/marker_defect_m.png',
            'iconSize' => new Point(['x' => 28, 'y' => 43]),
            'iconAnchor' => new Point (['x' => 14, 'y' => 43]),
            'popupAnchor' => new Point (['x' => -3, 'y' => -76])
        ]);
        $objectIcon = new Icon([
            'iconUrl' => '/images/marker_object_m.png',
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
                $object = Objects::find()->where(['entityUuid' => $alarm["entityUuid"]])->one();
                if ($object["latitude"] > 0) {
                    $position = new LatLng(['lat' => $object["latitude"], 'lng' => $object["longitude"]]);
                    $marker = new Marker(['latLng' => $position, 'popupContent' => '<b>'
                        . $object["title"] . '</b><br/>'
                        . $alarm["title"]]);
                    $marker->setIcon($alarmIcon);
                    $alarmGroup->addLayer($marker);
                }
            }
        }

        $objectSelect = Objects::find()
            ->select('_id, title, latitude, longitude')
            ->where(['deleted' => 0])
            ->asArray()
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

        $layer['objectGroup'] = $objectsGroup;
        $layer['alarmGroup'] = $alarmGroup;
        $layer['coordinates'] = $coordinates;
        return $layer;
    }

}
