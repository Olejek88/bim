<?php


namespace frontend\controllers;


use common\datasource\DataSourceTrait;
use common\datasource\IDataSource;
use common\models\DataSource;
use Yii;
use yii\base\Module;
use yii\helpers\Html;

class DataSourceController extends PoliterController
{
    protected $modelClass = DataSource::class;

    public function actionIndex()
    {
        $modules = Yii::$app->getModules();
        foreach ($modules as $prefix => $module) {
            /** @var $module IDataSource|DataSourceTrait|Module */
            if (is_array($module) && !empty($module['class']) && str_contains($module['class'], 'common\datasource\\')) {
                $module = Yii::$app->getModule($prefix);
            } else if (is_object($module) && str_contains($module::className(), 'common\datasource\\')) {
            } else {
                continue;
            }

            echo Html::a($module->description, '/' . $prefix) . '<br/>';
        }
    }
}