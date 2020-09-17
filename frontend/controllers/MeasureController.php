<?php

namespace frontend\controllers;

use common\models\Measure;
use common\models\MeasureChannel;
use Exception;
use frontend\models\MeasureSearch;
use Throwable;
use Yii;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * MeasureController implements the CRUD actions for Measure model.
 */
class MeasureController extends PoliterController
{
    protected $modelClass = Measure::class;

    /**
     * Deletes an existing Measure model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
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
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * Finds the Measure model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id Id
     *
     * @return Measure the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Measure::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Запрашиваемая страница не существует.'));
        }
    }

    /**
     * Lists all Measures models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MeasureSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 50;
        $channels = MeasureChannel::find()->orderBy('title')->all();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'channels' => $channels
        ]);
    }

    /**
     * функция отрабатывает сигналы от дерева и выполняет добавление нового оборудования
     *
     * @return mixed
     */
    public
    function actionNew()
    {
        $measure = new Measure();
        $request = Yii::$app->request;
        $measureChannelUuid = $request->getQueryParam('measureChannelUuid');
        $channels = MeasureChannel::find()->orderBy('title')->all();
        $channels = ArrayHelper::map($channels, 'uuid', 'title');
        return $this->renderAjax('_add_form', [
            'measure' => $measure,
            'measureChannelUuid' => $measureChannelUuid,
            'channels' => $channels
        ]);
    }

    /**
     * Creates a new model.
     * @return mixed
     */
    public
    function actionSave()
    {
        $model = new Measure();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->save(false)) {
                return $this->redirect(parse_url($_SERVER["HTTP_REFERER"], PHP_URL_PATH) . '?node=' . $model['_id'] . 'k');
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
