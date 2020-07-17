<?php

namespace frontend\controllers;

use common\components\MainFunctions;
use common\models\ActionRegister;
use common\models\Alarm;
use common\models\Objects;
use common\models\ObjectType;
use Exception;
use frontend\models\AlarmSearch;
use Throwable;
use Yii;
use yii\bootstrap\ActiveForm;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * AlarmController implements the CRUD actions for Alarm model.
 */
class AlarmController extends PoliterController
{
    protected $modelClass = Alarm::class;

    /**
     * Deletes an existing Alarm model.
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
        $model = $this->findModel($id);
        MainFunctions::register(Yii::t('app', 'Удалено предупреждение ') . $model->title,
            ActionRegister::TYPE_DELETE,
            $model->entityUuid . "");
        $model->delete();
        return $this->redirect(parse_url($_SERVER["HTTP_REFERER"], PHP_URL_PATH));
    }

    /**
     * Finds the Alarm model based on its primary key value.
     *
     * @param integer $id Id
     *
     * @return Alarm the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Alarm::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Запрашиваемая страница не существует.'));
        }
    }

    /**
     * Lists all Alarms models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AlarmSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 50;
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Lists all Alarms for Object
     * @return mixed
     */
    public function actionList()
    {
        $searchModel = new AlarmSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 50;

        $request = Yii::$app->request;
        $objectUuid = $request->get('uuid');
        $dataProvider->query->where(['entityUuid' => $objectUuid]);

        return $this->renderAjax('_list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'objectUuid' => $objectUuid
        ]);
    }

    /**
     * @return mixed
     */
    public
    function actionNew()
    {
        $alarm = new Alarm();
        $objects = Objects::find()->where(['objectTypeUuid' => ObjectType::OBJECT])->orderBy('title')->all();
        $objects = ArrayHelper::map($objects, 'uuid', 'title');

        $request = Yii::$app->request;
        $objectUuid = $request->get('objectUuid');
        return $this->renderAjax('_add_form', [
            'alarm' => $alarm,
            'objects' => $objects,
            'objectUuid' => $objectUuid
        ]);
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public
    function actionSave()
    {
        $model = new Alarm();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save(false)) {
                MainFunctions::register(Yii::t('app', 'Добавлено предупреждение ') . $model->title,
                    ActionRegister::TYPE_ADD,
                    $model->entityUuid . "");
                $return['code'] = 0;
                $return['message'] = "";
                return json_encode($return);
            } else {
                $message = '';
                foreach ($model->errors as $key => $error) {
                    $message .= $error[0] . '</br>';
                }
                return json_encode(['message' => $message]);
            }
        }
        return $this->render('_add_form', [
            'alarm' => $model
        ]);
    }

    /**
     * Ajax validation
     *
     * @return array
     *
     */
    public function actionValidation()
    {
        $model = new Alarm();
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = 'json';
            return ActiveForm::validate($model);
        }
        return null;
    }
}
