<?php

namespace frontend\controllers;

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
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * Build tree of equipment by user
     *
     * @return mixed
     */
    public function actionTree()
    {
        //const REGION = '4F7BC1D4-EA62-400F-9EC2-1BA289C7FCE2';
        //const DISTRICT = '40876BDE-4933-443E-B3D1-5E8610DE8E24';
        //const CITY = '9270E308-6125-42D3-93AD-A976E3DD5D2F';
        //const CITY_DISTRICT = '461CDEBF-A320-4A96-BE6E-788B3E9267CF';
        //const SUB_DISTRICT = '6A131086-AEAA-4513-8C9D-3CEAA979A2EC';
        //const STREET = 'B23955CC-13AC-4D17-9394-5928C3D0A321';
        //const OBJECT = 'A20871DA-A65D-42CB-983F-0B106C507F29';
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
                        'folder' => true
                    ];
                    $objects = Objects::find()
                        ->where(['objectTypeUuid' => ObjectType::OBJECT])
                        ->andWhere(['parentUuid' => $city_districts['uuid']])
                        ->andWhere(['deleted' => 0])
                        ->all();
                    foreach ($objects as $object) {
                        $childIdx3 = count($fullTree['children'][$childIdx]['children'][$childIdx2]['children']) - 1;
                        $fullTree['children'][$childIdx]['children'][$childIdx2]['children'][$childIdx3]['children'][] = [
                            'title' => $object->getFullTitle(),
                            'folder' => false
                        ];
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

        return $this->renderAjax('_add_form', [
            'object' => $object,
            'objects' => $objects,
            'objectSubTypes' => $objectSubTypes,
            'objectTypes' => $objectTypes
        ]);
    }

    /**
     * функция отрабатывает сигналы от дерева и выполняет добавление нового оборудования или объекта
     *
     * @return mixed
     */
    public function actionAdd()
    {
        if (isset($_POST["selected_node"])) {
            $folder = $_POST["folder"];
            if (isset($_POST["uuid"]))
                $uuid = $_POST["uuid"];
            else $uuid = 0;
            if (isset($_POST["type"]))
                $type = $_POST["type"];
            else $type = 0;

            if ($folder == "true" && $uuid && $type) {
                if ($type == 'street') {
                    $house = new House();
                    return $this->renderAjax('_add_house_form', [
                        'streetUuid' => $uuid,
                        'house' => $house
                    ]);
                }
                if ($type == 'house') {
                    $object = new Objects();
                    return $this->renderAjax('_add_object_form', [
                        'houseUuid' => $uuid,
                        'object' => $object
                    ]);
                }
            }
        }
        return 'Нельзя добавить объект в этом месте';
    }

    /**
     * функция отрабатывает сигналы от дерева и выполняет редактирование оборудования
     *
     * @return mixed
     * @throws InvalidConfigException
     */
    public function actionEdit()
    {
        if (!Yii::$app->user->can(User::PERMISSION_ADMIN)) {
            return 'Нет прав.';
        }

        if (isset($_POST["selected_node"])) {
            if (isset($_POST["uuid"]))
                $uuid = $_POST["uuid"];
            else $uuid = 0;
            if (isset($_POST["type"]))
                $type = $_POST["type"];
            else $type = 0;

            if ($uuid && $type) {
                if ($type == 'street') {
                    $street = Street::find()->where(['uuid' => $uuid])->one();
                    if ($street) {
                        return $this->renderAjax('_add_street_form', [
                            'street' => $street,
                            'streetUuid' => $uuid
                        ]);
                    }
                }
                if ($type == 'house') {
                    $house = House::find()->where(['uuid' => $uuid])->one();
                    if ($house) {
                        return $this->renderAjax('_add_house_form', [
                            'houseUuid' => $uuid,
                            'house' => $house
                        ]);
                    }
                }

                if ($type == 'object') {
                    $object = Objects::find()->where(['uuid' => $uuid])->limit(1)->one();
                    if ($object) {
                        return $this->renderAjax('_add_object_form', [
                            'objectUuid' => $uuid,
                            'object' => $object
                        ]);
                    }
                }
            }
        }
        return 'Нельзя отредактировать этот объект';
    }

    /**
     * функция отрабатывает сигналы от дерева и выполняет удаление
     *
     * @return mixed
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function actionRemove()
    {
        if (!Yii::$app->user->can(User::PERMISSION_ADMIN)) {
            return 'Нет прав.';
        }

        if (isset($_POST["selected_node"])) {
            if (isset($_POST["uuid"]))
                $uuid = $_POST["uuid"];
            else $uuid = 0;
            if (isset($_POST["type"]))
                $type = $_POST["type"];
            else $type = 0;

            if ($uuid && $type) {
                if ($type == 'street') {
                    $street = Street::find()->where(['uuid' => $uuid])->limit(1)->one();
                    if ($street) {
                        $house = House::find()->where(['streetUuid' => $street['uuid']])->limit(1)->one();
                        if (!$house) {
                            $street->delete();
                        }
                    }
                }
                if ($type == 'house') {
                    $house = House::find()->where(['uuid' => $uuid])->limit(1)->one();
                    if ($house) {
                        $object = Objects::find()->where(['houseUuid' => $house['uuid']])->limit(1)->one();
                        if (!$object) {
                            $house->delete();
                        }
                    }
                }
                if ($type == 'object') {
                    $object = Objects::find()->where(['uuid' => $uuid])->one();
                    if ($object) {
                        $object['deleted'] = true;
                        $object->save();
                    }

                }
            }
        }
        return 'Нельзя удалить этот объект';
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
}
