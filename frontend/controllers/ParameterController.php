<?php

namespace frontend\controllers;

use common\components\MainFunctions;
use common\models\ActionRegister;
use common\models\Parameter;
use common\models\ParameterType;
use Exception;
use frontend\models\ParameterSearch;
use Throwable;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * ParameterController implements the CRUD actions for Parameter model.
 */
class ParameterController extends PoliterController
{
    protected $modelClass = Parameter::class;

    /**
     * Lists all Parameter models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        if (isset($_POST['editableAttribute'])) {
            $model = Parameter::find()
                ->where(['_id' => $_POST['editableKey']])
                ->one();
            if ($_POST['editableAttribute'] == 'value') {
                $model['value'] = $_POST['Parameter'][$_POST['editableIndex']]['value'];
            }
            if ($_POST['editableAttribute'] == 'parameterTypeUuid') {
                $model['parameterTypeUuid'] = $_POST['Parameter'][$_POST['editableIndex']]['parameterTypeUuid'];
            }
            $model->save();
            return json_encode('');
        }

        $searchModel = new ParameterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 15;
        $parameterTypes = ParameterType::find()->orderBy('title')->all();
        return $this->render(
            'index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'parameterTypes' => $parameterTypes
            ]
        );
    }

    /**
     * Deletes an existing Parameter model.
     *
     * @param integer $id Id
     *
     * @return mixed
     * @throws NotFoundHttpException
     * @throws Exception
     * @throws Throwable
     */
    public function actionDelete($id)
    {
        /** @var Parameter $parameter */
        $parameter = $this->findModel($id);
        if ($parameter) {
            MainFunctions::register(Yii::t('app', 'Удален параметр ')
                . $parameter->parameterType->title
                . " для " . $parameter->getEntityTitle(),
                ActionRegister::TYPE_DELETE,
                $parameter->uuid);
            $parameter->delete();
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the Parameter model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id Id
     *
     * @return Parameter the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Parameter::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Запрашиваемая страница не существует.'));
        }
    }

    /**
     * Lists all Parameters models.
     * @param $uuid
     * @return mixed
     */
    public function actionList($uuid)
    {
        if (isset($_POST['editableAttribute'])) {
            $model = Parameter::find()
                ->where(['_id' => $_POST['editableKey']])
                ->one();
            if ($_POST['editableAttribute'] == 'value') {
                $model['value'] = $_POST['Parameter'][$_POST['editableIndex']]['value'];
            }
            if ($_POST['editableAttribute'] == 'parameterTypeUuid') {
                $model['parameterTypeUuid'] = $_POST['Parameter'][$_POST['editableIndex']]['parameterTypeUuid'];
            }
            $model->save();
            return json_encode('');
        }

        $searchModel = new ParameterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 15;
        $dataProvider->query->where(['entityUuid' => $uuid]);
        $parameterTypes = ParameterType::find()->orderBy('title')->all();

        return $this->renderAjax('_parameter_list', [
            'dataProvider' => $dataProvider,
            'parameterTypes' => $parameterTypes,
            'objectUuid' => $uuid
        ]);
    }

    /**
     * функция выполняет добавление нового параметра
     *
     * @return mixed
     */
    public
    function actionNew()
    {
        $parameter = new Parameter();
        $request = Yii::$app->request;
        $objectUuid = $request->get('objectUuid');
        $parameterTypes = ParameterType::find()->orderBy('title')->all();
        $parameterTypes = ArrayHelper::map($parameterTypes, 'uuid', 'title');

        return $this->renderAjax('_add_form', [
            'parameter' => $parameter,
            'entityUuid' => $objectUuid,
            'parameterTypes' => $parameterTypes
        ]);
    }

    /**
     * Creates a new model.
     * @return mixed
     * @throws Exception
     */
    public
    function actionSave()
    {
        $model = new Parameter();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save(false)) {
                MainFunctions::register(Yii::t('app', 'Добавлен параметр ')
                    . $model->parameterType->title
                    . " для " . $model->getEntityTitle(),
                    ActionRegister::TYPE_ADD,
                    $model->uuid);
                return $this->redirect('../parameter/index');
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
}
