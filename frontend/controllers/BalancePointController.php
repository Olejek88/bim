<?php

namespace backend\controllers;

use backend\models\ServiceSearch;
use common\models\Service;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * ServiceController implements the CRUD actions for Service model.
 */
class ServiceController extends PoliterController
{
    protected $modelClass = Service::class;

    /**
     * Lists all Equipment models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        Service::updateAll(['status' => 0],
            'last_start_date is null or unix_timestamp() > (unix_timestamp(last_start_date) + delay)');
        if (isset($_POST['editableAttribute'])) {
            $model = Service::find()
                ->where(['_id' => $_POST['editableKey']])
                ->one();
            if ($_POST['editableAttribute'] == 'title') {
                $model['title'] = $_POST['Service'][$_POST['editableIndex']]['title'];
            }
            if ($_POST['editableAttribute'] == 'service_name') {
                $model['service_name'] = $_POST['Service'][$_POST['editableIndex']]['service_name'];
            }
            if ($_POST['editableAttribute'] == 'status') {
                $model['status'] = $_POST['Service'][$_POST['editableIndex']]['status'];
            }
            if ($_POST['editableAttribute'] == 'delay') {
                $model['delay'] = $_POST['Service'][$_POST['editableIndex']]['delay'];
            }
            if ($_POST['editableAttribute'] == 'active') {
                $model['active'] = $_POST['Service'][$_POST['editableIndex']]['active'];
            }
            $model->save();
            return json_encode('');
        }

        $searchModel = new ServiceSearch();
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
     * Displays a single Service model.
     *
     * @param integer $id Id
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        return $this->render(
            'view',
            [
                'model' => $this->findModel($id),
            ]
        );
    }

    /**
     * Finds the Service model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id Id
     *
     * @return Service the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Service::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Запрашиваемая страница не существует.'));
        }
    }

    /**
     * Creates a new Service model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Service();
        $searchModel = new ServiceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 10;
        $dataProvider->setSort(['defaultOrder' => ['_id' => SORT_DESC]]);

        if ($model->load(Yii::$app->request->post())) {
            // проверяем все поля, если что-то не так показываем форму с ошибками
            if (!$model->validate()) {
                return $this->render('create', ['model' => $model]);
            }

            // сохраняем запись
            if ($model->save(false)) {
                return $this->redirect(['view', 'id' => $model->_id]);
            }
        }
        return $this->render('create', ['model' => $model, 'dataProvider' => $dataProvider]);
    }

    /**
     * Updates an existing Service model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id Id
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        // TODO: реализовать перенос файлов документации в новый каталог
        // если изменилась модель оборудования при редактировании оборудования!
        // так как файлы документации должны храниться в папке с uuid
        // модели оборудования

        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            // сохраняем модель
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->_id]);
            } else {
                return $this->render(
                    'update',
                    [
                        'model' => $model,
                    ]
                );
            }
        } else {
            return $this->render(
                'update',
                [
                    'model' => $model,
                ]
            );
        }
    }

    /**
     * Deletes an existing Service model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id Id
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        //$this->findModel($id)->delete();
        return $this->redirect(['index']);
    }
}
