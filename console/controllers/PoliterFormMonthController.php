<?php

namespace console\controllers;

use common\components\MainFunctions;
use common\models\Measure;
use common\models\MeasureChannel;
use common\models\MeasureType;
use common\models\Objects;
use Exception;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * Class PoliterFormMonthController
 * @package console\controllers
 */
class PoliterFormMonthController extends Controller
{
    public $defaultAction = 'run';

    /**
     * @return int
     * @throws Exception
     */
    public function actionRun()
    {
        // 1. check all objects / channels if (source present) create channel
        self::createChannels();
        // 2. get all data by days and store as month
        self::storeData();
        return ExitCode::OK;
    }

    /**
     * @throws Exception
     */
    function createChannels()
    {
        $objects = Objects::find()->all();
        foreach ($objects as $object) {
            self::createChannel(MeasureType::HEAT_CONSUMED, 'Архив тепловой энергии по месяцам', $object['uuid']);
            if ($object['water']) {
                self::createChannel(MeasureType::COLD_WATER, 'Архив ХВС по месяцам', $object['uuid']);
            }
            if ($object['electricity']) {
                self::createChannel(MeasureType::ENERGY, 'Архив электроэнергии по месяцам', $object['uuid']);
            }
        }
    }

    /**
     * @param $measureTypeUuid
     * @param $title
     * @param $objectUuid
     * @throws Exception
     */
    function createChannel($measureTypeUuid, $title, $objectUuid)
    {
        $measureChannel = MeasureChannel::find()
            ->where(['type' => MeasureType::MEASURE_TYPE_MONTH])
            ->andWhere(['objectUuid' => $objectUuid])
            ->andWhere(['measureTypeUuid' => $measureTypeUuid])
            ->one();
        $measureChannelDay = MeasureChannel::find()
            ->where(['type' => MeasureType::MEASURE_TYPE_DAYS])
            ->andWhere(['objectUuid' => $objectUuid])
            ->andWhere(['measureTypeUuid' => $measureTypeUuid])
            ->one();
        //echo 'check channel '.$measureTypeUuid.' '.json_encode($measureChannel).' '.json_encode($measureChannelDay).PHP_EOL;
        if ($measureChannel == null && $measureChannelDay != null) {
            $measureChannel = new MeasureChannel();
            $measureChannel->uuid = MainFunctions::GUID();
            $measureChannel->title = $title;
            $measureChannel->objectUuid = $objectUuid;
            $measureChannel->measureTypeUuid = $measureTypeUuid;
            $measureChannel->type = MeasureType::MEASURE_TYPE_MONTH;
            $measureChannel->path = "internal";
            $measureChannel->original_name = "";
            $measureChannel->param_id = $measureChannel->path . Yii::$app->security->generateRandomString(16);
            $measureChannel->data_source = $measureChannelDay->data_source;
            if ($measureChannel->save()) {
                $measureChannel->param_id = $measureChannel->path . $measureChannel->_id;
                if ($measureChannel->save()) {
                    echo 'create new channel ' . $measureChannel->title . PHP_EOL;
                } else {
                    echo 'error create new channel ' . json_encode($measureChannel->errors) . PHP_EOL;
                }
            } else {
                echo 'error create new channel ' . json_encode($measureChannel->errors) . PHP_EOL;
            }
        }
    }

    /**
     * @throws Exception
     */
    function storeData()
    {
        $channels = MeasureChannel::find()->where(['type' => MeasureType::MEASURE_TYPE_MONTH])->all();
        foreach ($channels as $channel) {
            $deep = 35;
            $today = getdate();
            $year = $today['year'];
            $month = $today['mon'];
            echo '[' . $channel['_id'] . '] ' . $channel['title'] . PHP_EOL;
            $channelDay = MeasureChannel::find()
                ->where(['_id' => $channel['param_id']])
                ->one();
            if ($channelDay) {
                while ($deep-- > 0) {
                    $startDate = sprintf("%04d%02d01000000", $year, $month);
                    $endDate = sprintf("%04d%02d31000000", $year, $month);
                    $sum = Measure::find()
                        ->where(['measureChannelId' => $channelDay['_id']])
                        ->andWhere(['>=', 'date', $startDate])
                        ->andWhere(['<=', 'date', $endDate])
                        ->andWhere(['>', 'value', 0])
                        ->sum('value');
                    //echo $deep.' check '.$channel['uuid'].' '.$startDate.'-'.$endDate.' = '.$sum.PHP_EOL;
                    if ($sum && is_numeric($sum)) {
                        self::storeCheckMeasure($channel['uuid'], $startDate, number_format($sum, 3));
                    }
                    if ($month > 1) {
                        $month--;
                    } else {
                        $month = 12;
                        $year--;
                    }
                }
            }
        }
    }

    /**
     * @param $measureChannelId
     * @param $date
     * @param $sum
     * @throws Exception
     */
    function storeCheckMeasure($measureChannelId, $date, $sum)
    {
        $measureMonth = Measure::find()
            ->where(['measureChannelId' => $measureChannelId])
            ->andWhere(['date' => $date])
            ->limit(1)
            ->one();
        if (!$measureMonth) {
            $measure = new Measure();
            $measure->measureChannelId = $measureChannelId;
            $measure->date = $date;
            $measure->value = $sum;
            if ($measure->save()) {
                echo 'store data ' . $date . ' ' . $sum . PHP_EOL;
            } else {
                echo 'store data problem ' . json_encode($measure->errors) . PHP_EOL;
            }
        } elseif ($measureMonth['value'] != $sum) {
            $measureMonth['value'] = $sum;
            if ($measureMonth->save()) {
                echo 'update data ' . $date . ' ' . $sum . PHP_EOL;
            } else {
                echo 'update data problem ' . json_encode($measureMonth->errors) . PHP_EOL;
            }
        }
    }
}
