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

        $mcs = MeasureChannel::find()->where(['deleted' => false])->select(['uuid', 'param_id', 'type'])->asArray()->all();
        foreach ($mcs as $mc) {
            $archives = FlowArchive::find()->where(['ID' => $mc['param_id']])->asArray()->all();
            foreach ($archives as $archive) {
                $m = new Measure();
                $m->uuid = MainFunctions::GUID();
                $m->measureChannelUuid = $mc['uuid'];
                $m->date = FlowArchive::getDatetime($archive['TIME']);
                $m->value = floatval(FlowArchive::getFloatValue($archive['VALUE']));
                if (!$m->save()) {
                    // TODO: запротоколировать ошибки записи
                }
            }
        }

        return ExitCode::OK;
    }
}
