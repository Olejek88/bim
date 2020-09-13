<?php

namespace console\controllers;

use common\components\MainFunctions;
use common\models\FlowArchive;
use common\models\Measure;
use common\models\MeasureChannel;
use Exception;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * Class PoliterAccessController
 * @package console\controllers
 */
class PoliterGetAllMeasureController extends Controller
{
    public $defaultAction = 'run';

    /**
     * @return int
     * @throws Exception
     */
    public function actionRun()
    {
//        $this->stdout("Hello?\n $dbName \n", Console::BOLD|Console::FG_RED);
        $mcs = MeasureChannel::find()
            ->where(['deleted' => false])
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
