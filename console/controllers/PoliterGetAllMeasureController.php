<?php

namespace console\controllers;

use common\components\MainFunctions;
use common\datasource\DataSourceTrait;
use common\datasource\politer\models\FlowArchive;
use common\models\Measure;
use common\models\MeasureChannel;
use Exception;
use Yii;
use yii\base\Module;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

/**
 * Class PoliterAccessController
 * @package console\controllers
 */
class PoliterGetAllMeasureController extends Controller
{
    public $defaultAction = 'run';
    public $pmodule;

    /**
     * @param $actionID
     * @return array|string[]
     */
    public function options($actionID)
    {
        $options = parent::options($actionID);
        $options[] = 'pmodule';
        return $options;
    }

    /**
     * @return int
     * @throws Exception
     */
    public function actionRun()
    {
        if ($this->pmodule == null) {
            $this->stdout("You must specify --pmodule" . PHP_EOL, Console::BOLD | Console::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        } else {
            /** @var DataSourceTrait|Module $module */
            $module = Yii::$app->getModule($this->pmodule);
            if ($module == null) {
                $this->stdout("You specify wrong --pmodule={$this->pmodule}" . PHP_EOL, Console::BOLD | Console::FG_RED);
                return ExitCode::UNSPECIFIED_ERROR;
            } else if (!str_contains($module::className(), 'common\datasource\politer\\')) {
                $this->stdout("{$this->pmodule} is not politer module" . PHP_EOL, Console::BOLD | Console::FG_RED);
                return ExitCode::UNSPECIFIED_ERROR;
            }
        }

        $this->stdout("pmodule: {$module->description}" . PHP_EOL, Console::BOLD | Console::FG_GREEN);

        $mcs = MeasureChannel::find()
            ->where(['deleted' => false, 'data_source' => $module->id])
            ->andWhere(['>', 'param_id', 0])
            ->select(['uuid', 'param_id', 'type'])->asArray()->all();
        $count = count($mcs);
        $cnt = 1;
        // временное решение, чтобы не тянуть уже скачанное с нуля
        foreach ($mcs as $mc) {
            $measureCount = Measure::find()->where(['measureChannelId' => $mc['_id']])->count();
            if ($measureCount < 10) {
                echo date('H:i:s') . ' [' . $cnt . '/' . $count . ' ' . number_format($cnt * 100 / $count, 2) . '%] channel ' . $mc['param_id'] . ' ' . $measureCount . PHP_EOL;
                $archives = FlowArchive::find()->where(['ID' => $mc['param_id']])->asArray()->all();
                foreach ($archives as $archive) {
                    $m = new Measure();
                    $m->measureChannelId = $mc['_id'];
                    $m->date = FlowArchive::getDatetime($archive['TIME']);
                    $m->value = floatval(FlowArchive::getFloatValue($archive['VALUE']));
                    if (!$m->save()) {
                        // TODO: запротоколировать ошибки записи
                    }
                }
            } else {
                echo date('H:i:s') . ' [' . $cnt . '/' . $count . ' ' . number_format($cnt * 100 / $count, 2) . '%] skip channel ' . $mc['param_id'] . ' ' . $measureCount . PHP_EOL;
            }
            $cnt++;
        }

        return ExitCode::OK;
    }
}
