<?php

namespace frontend\controllers;

use common\models\BalancePoint;
use frontend\models\BalancePointSearch;
use Yii;
use yii\db\StaleObjectException;
use yii\web\NotFoundHttpException;

/**
 * BalancePointController implements the CRUD actions for BalancePoint model.
 */
class BalancePointController extends PoliterController
{
    protected $modelClass = BalancePoint::class;

    /**
     * Lists all Equipment models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        if (isset($_POST['editableAttribute'])) {
            $model = BalancePoint::find()
                ->where(['_id' => $_POST['editableKey']])
                ->one();
            if ($_POST['editableAttribute'] == 'measureChannelUuid') {
                $model['measureChannelUuid'] = $_POST['Service'][$_POST['editableIndex']]['measureChannelUuid'];
            }
            if ($_POST['editableAttribute'] == 'objectUuid') {
                $model['objectUuid'] = $_POST['Service'][$_POST['editableIndex']]['objectUuid'];
            }
            if ($_POST['editableAttribute'] == 'input') {
                $model['input'] = $_POST['Service'][$_POST['editableIndex']]['input'];
            }
            $model->save();
            return json_encode('');
        }

        $searchModel = new BalancePointSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 50;

        return $this->render(
            'index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]
        );
    }

    /**
     * Finds the BalancePoint model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id Id
     *
     * @return BalancePoint the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BalancePoint::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Запрашиваемая страница не существует.'));
        }
    }

    /**
     * Deletes an existing BalancePoint model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id Id
     *
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * функция отрабатывает сигналы от дерева и выполняет добавление нового
     *
     * @return mixed
     */
    public
    function actionNew()
    {
        $balancePoint = new BalancePoint();
        return $this->renderAjax('_add_form', [
            'model' => $balancePoint
        ]);
    }

    /**
     * Creates a new model.
     * @return mixed
     */
    public
    function actionSave()
    {
        $model = new BalancePoint();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save(false)) {
                return $this->redirect('../balance-point/index');
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
