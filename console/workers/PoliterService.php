<?php

namespace console\workers;

use common\components\MainFunctions;
use common\models\FlowArchive;
use common\models\Flows2;
use common\models\Measure;
use common\models\MeasureChannel;
use Exception;
use inpassor\daemon\Worker;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

class PoliterService extends Worker
{
    const LOG_ID = "politer";

    public function init()
    {
        $this->logFile = '@console/runtime/daemon/logs/worker-politer.log';
        $this->errorLogFile = '@console/runtime/daemon/logs/worker-politer-error.log';
        parent::init();
    }

    /**
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function run()
    {
        $this->log('[' . self::LOG_ID . '] start worker');

        // тянем текущие значения
        $currentDate = date('Y-m-d H:i:s');
        self::getExtMeasuredValues($currentDate);

        // пытаемся получить измерения не полученные ранее
        // TODO: дёргать их так же часто как текущие не нужно, нужно реализовать ограничение
        self::getExtLostMeasure();

        $this->log('[' . self::LOG_ID . '] stop worker');
    }

    /**
     * @param $date
     * @throws Exception
     */
    private function getExtMeasuredValues($date)
    {
        $mcs = MeasureChannel::find()->where(['deleted' => false])->select(['uuid', 'param_id', 'type'])->asArray()->all();
        $ids = [];
        $uuids = [];
        foreach ($mcs as $mc) {
            $ids[] = $mc['param_id'];
            $uuids[] = $mc['uuid'];
        }

        $measures = Measure::find()
            ->where(['measureChannelUuid' => $uuids,])
            ->andWhere([
                'AND',
                ['>=', 'date', $date],
                ['<', 'date', date('Y-m-d', strtotime($date . '+1 day'))],
            ])
            ->all();
        $measures = ArrayHelper::map($measures, 'measureChannelUuid', function ($item) {
            return $item;
        });

        $oracleDateFormat = 'YYYY.MM.DD HH24:MI:SS';
        $flows2 = Flows2::find()
            ->where(['ID' => $ids])
            ->andWhere("TIME >= TO_TIMESTAMP(:startTime, '$oracleDateFormat')", [':startTime' => $date])
            ->andWhere("TIME < TO_TIMESTAMP(:endTime, '$oracleDateFormat')", [':endTime' => date('Y-m-d 00:00:00', strtotime($date . '+1 day'))])
            ->asArray()
            ->all();
        $flows2 = ArrayHelper::map($flows2, 'ID', function ($item) {
            return $item;
        });

        foreach ($mcs as $mc) {
            /** @var Measure $m */
            $m = null;
            if (!empty($measures[$mc['uuid']]) && !empty($flows2[$mc['param_id']])) {
                // обновляем запись с измерением
                $m = $measures[$mc['uuid']];
            } else if (empty($measures[$mc['uuid']]) && !empty($flows2[$mc['param_id']])) {
                // создаём запись с измерением
                $m = new Measure();
                $m->uuid = MainFunctions::GUID();
                $m->measureChannelUuid = $mc['uuid'];
                $m->type = $mc['type'];
            } else {
                // нет записи с измерением, нет данных для создания записи
                continue;
            }

            $m->value = floatval(Flows2::getFloatValue($flows2[$mc['param_id']]['VALUE']));
            $m->date = Flows2::getDatetime($flows2[$mc['param_id']]['TIME']);

            if (!$m->save()) {
                // TODO: запротоколировать ошибки записи
            }

        }
    }

    /**
     * @throws InvalidConfigException
     * @throws Exception
     */
    private function getExtLostMeasure()
    {
        // как глубоко будем проверять пропущенные значения
        $deep = -7;
        $mcs = MeasureChannel::find()->where(['deleted' => false])->select(['uuid', 'param_id', 'type'])->asArray()->all();
        $mcs = ArrayHelper::map($mcs, 'uuid', function ($item) {
            return $item;
        });

        $measuresByChannels = ArrayHelper::map($mcs, 'uuid', function ($item) {
            return [];
        });

        $uuids = [];
        foreach ($mcs as $mc) {
            $uuids[] = $mc['uuid'];
        }

        $date = '2020-06-11 00:00:00';
        $measures = Measure::find()
            ->where(['measureChannelUuid' => $uuids])
            ->andWhere([
                'AND',
                ['>=', 'date', date('Y-m-d', strtotime($date . '' . $deep . ' day'))],
                ['<', 'date', $date],
            ])
            ->asArray()
            ->orderBy(['date' => SORT_ASC])
            ->all();
        foreach ($measures as $measure) {
            $measuresByChannels[$measure['measureChannelUuid']][$measure['uuid']] = $measure;
        }

        unset($measures);

        foreach ($measuresByChannels as $channelUuid => $channel) {
            $absDeep = abs(intval($deep));
            $dateList = [];
            for ($i = 1; $i <= $absDeep; $i++) {
                $dateList[date('Ymd', strtotime($date . '-' . $i . ' day'))] = null;
            }

            foreach ($channel as $measure) {
                $measureDate = date('Ymd', strtotime($measure['date']));
                unset($dateList[$measureDate]);
            }

            foreach ($dateList as $lostDate => $value) {
                $archive = FlowArchive::find()
                    ->where(['ID' => $mcs[$channelUuid]['param_id'],
                        'fromTime' => date('Y-m-d 00:00:00', strtotime($lostDate)),
                        'toTime' => date('Y-m-d 00:00:00', strtotime($lostDate . '+1 day')),
                    ])->one();
                if ($archive) {
                    $m = new Measure();
                    $m->uuid = MainFunctions::GUID();
                    $m->measureChannelUuid = $channelUuid;
                    $m->date = FlowArchive::getDatetime($archive['TIME']);
                    $m->value = floatval(FlowArchive::getFloatValue($archive['VALUE']));
                    $m->type = $mcs[$channelUuid]['type'];
                    if (!$m->save()) {
                        // TODO: запротоколировать ошибки записи
                    }

                }
            }
        }
    }
}