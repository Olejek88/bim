<?php

namespace frontend\controllers;

use common\components\MainFunctions;
use common\models\ActionRegister;
use common\models\Attribute;
use common\models\AttributeType;
use common\models\Objects;
use common\models\ObjectType;
use Exception;
use frontend\models\AttributeSearch;
use Yii;
use yii\bootstrap\ActiveForm;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * AttributeController implements the CRUD actions for Attribute model.
 */
class AttributeController extends PoliterController
{
    protected $modelClass = Attribute::class;

    /**
     * Lists all Attribute models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AttributeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 15;
        $attributeTypes = AttributeType::find()->orderBy('title')->all();
        return $this->render(
            'index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'attributeTypes' => $attributeTypes
            ]
        );
    }

    /**
     * Deletes an existing Attribute model.
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
        $model = $this->findModel($id);
        MainFunctions::register(Yii::t('app', 'Удален атрибут ') . $model->title,
            ActionRegister::TYPE_DELETE,
            $model->entityUuid . "");
        $model->delete();
        return $this->redirect(parse_url($_SERVER["HTTP_REFERER"], PHP_URL_PATH));
    }

    /**
     * Finds the Attribute model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id Id
     *
     * @return Attribute the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Attribute::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Запрашиваемая страница не существует.'));
        }
    }

    /**
     * Lists all Attributes for Object
     * @return mixed
     */
    public function actionList()
    {
        $searchModel = new AttributeSearch();
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
        $attribute = new Attribute();
        $objects = Objects::find()->where(['objectTypeUuid' => ObjectType::OBJECT])->orderBy('title')->all();
        $objects = ArrayHelper::map($objects, 'uuid', 'title');

        $request = Yii::$app->request;
        $objectUuid = $request->get('objectUuid');
        return $this->renderAjax('_add_form', [
            'attribute' => $attribute,
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
        $model = new Attribute();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save(false)) {
                MainFunctions::register(Yii::t('app', 'Добавлен атрибут ') . $model->title,
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
            'model' => $model
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
        $model = new Attribute();
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = 'json';
            return ActiveForm::validate($model);
        }
        return null;
    }
}
