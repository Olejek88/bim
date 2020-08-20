<?php

namespace console\controllers;

use common\components\MainFunctions;
use common\models\Measure;
use common\models\MeasureChannel;
use common\models\MeasureType;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use Yii;
use yii\console\Controller;

/**
 * Class ExportController
 * @package console\controllers
 */
class ExportController extends Controller
{
    const LOG_ID = 'export';

    /**
     * @throws \Exception
     */
    public function actionTemperature()
    {
        echo ('[' . self::LOG_ID . '] start import temperature') . PHP_EOL;
        echo ('[' . self::LOG_ID . '] [' . Yii::$app->db->dsn . '] user/pass ' . Yii::$app->db->username) . PHP_EOL;
        $reader = new Xls();
        $file_name = Yii::$app->basePath . "/data/weather.xls";
        echo ('[' . self::LOG_ID . '] ' . $file_name) . PHP_EOL;
        $file = $reader->load($file_name);
        $sheet = $file->getActiveSheet();

        $measureChannelMonth = MeasureChannel::find()
            ->where(['measureTypeUuid' => MeasureType::TEMPERATURE_AIR])
            ->andWhere(['type' => MeasureType::MEASURE_TYPE_MONTH])
            ->andWhere(['path' => 'external'])
            ->limit(1)
            ->one();
        $measureChannelDay = MeasureChannel::find()
            ->where(['measureTypeUuid' => MeasureType::TEMPERATURE_AIR])
            ->andWhere(['type' => MeasureType::MEASURE_TYPE_DAYS])
            ->andWhere(['path' => 'external'])
            ->limit(1)
            ->one();
        if (!$measureChannelDay || !$measureChannelMonth) {
            echo ('[' . self::LOG_ID . '] каналы не созданы!') . PHP_EOL;
            return;
        }

        foreach ($sheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(FALSE);
            $cell_num = 0;
            $date = '';
            $value = '';
            $month = '';
            foreach ($cellIterator as $cell) {
                switch ($cell_num) {
                    case 0:
                        $date = $cell->getValue();
                        break;
                    case 2:
                        $value = '' . $cell->getFormattedValue();
                        break;
                    case 3:
                        $month = '' . $cell->getFormattedValue();
                        break;
                }
                $cell_num++;
            }
            echo $date . ' ' . $value . PHP_EOL;
            if ($date != '' && $value != '') {
                $realDate = strftime("%Y%m%d000000", strtotime($date));
                $data = Measure::find()
                    ->where(['measureChannelUuid' => $measureChannelDay['uuid']])
                    ->andWhere(['date' => $realDate])
                    ->one();
                if (!$data) {
                    $data = new Measure();
                    $data->uuid = MainFunctions::GUID();
                    $data->date = $realDate;
                    $data->measureChannelUuid = $measureChannelDay['uuid'];
                    $data->value = $value;
                    $data->save();
                }
                if ($month != '') {
                    $data = Measure::find()
                        ->where(['measureChannelUuid' => $measureChannelMonth['uuid']])
                        ->andWhere(['date' => $realDate])
                        ->one();
                    if (!$data) {
                        $data = new Measure();
                        $data->uuid = MainFunctions::GUID();
                        $data->date = $realDate;
                        $data->measureChannelUuid = $measureChannelMonth['uuid'];
                        $data->value = $month;
                        $data->save();
                    }
                }
            }
        }
    }
}