<?php

namespace console\workers;

use common\models\Measure;
use common\models\MeasureChannel;
use common\models\MeasureType;
use common\models\ServiceRegister;
use Exception;
use inpassor\daemon\Worker;

class ArchiveService extends Worker
{
    const LOG_ID = "archive";

    public function init()
    {
        $this->logFile = '@console/runtime/daemon/logs/worker-archive.log';
        $this->errorLogFile = '@console/runtime/daemon/logs/worker-archive-error.log';
        parent::init();
    }

    /**
     * @throws Exception
     */
    public function run()
    {
        $this->log('[' . self::LOG_ID . '] start worker');
        self::storeData();
        $this->log('[' . self::LOG_ID . '] stop worker');
    }

    /**
     * @throws Exception
     */
    function storeData()
    {
        $channels = MeasureChannel::find()->where(['type' => MeasureType::MEASURE_TYPE_MONTH])->all();
        foreach ($channels as $channel) {
            $deep = 2;
            $today = getdate();
            $year = $today['year'];
            $month = $today['mon'];
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
                        ->sum('value');
                    if ($sum) {
                        self::storeCheckMeasure($channel['_id'], $startDate, $sum);
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
            ->one();
        if (!$measureMonth) {
            $measure = new Measure();
            $measure->measureChannelId = $measureChannelId;
            $measure->date = $date;
            $measure->value = $sum;
            if ($measure->save()) {
                $this->log('[' . self::LOG_ID . '] store data ' . $date . ' ' . $sum);
                ServiceRegister::addServiceRegister(ServiceRegister::SERVICE_ARCHIVE, ServiceRegister::TYPE_INFO,
                    null,
                    'добавлено новое значение ' . $measure->measureChannel->object->getFullTitle() . ' ' . $measure->measureChannel->title . ' [' . $date . '] ' . $sum);
            } else {
                $this->log('[' . self::LOG_ID . '] store data problem ' . json_encode($measure->errors));
            }
        } elseif ($measureMonth['value'] != $sum) {
            $measureMonth['value'] = $sum;
            if ($measureMonth->save()) {
                $this->log('[' . self::LOG_ID . '] update data ' . $date . ' ' . $sum);
                echo 'update data ' . $date . ' ' . $sum . PHP_EOL;
            } else {
                $this->log('[' . self::LOG_ID . '] update data problem' . json_encode($measureMonth->errors));
            }
        }
    }
}