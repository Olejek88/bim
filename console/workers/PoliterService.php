<?php

namespace console\workers;

use common\components\MainFunctions;
use common\models\FlowArchive;
use common\models\Flows2;
use common\models\Measure;
use common\models\MeasureChannel;
use common\models\MeasureType;
use common\models\ServiceRegister;
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
//        $currentDate = '2020-06-10 11:00:00';
        self::getExtMeasuredValues($currentDate);

        // пытаемся получить измерения не полученные ранее
        // TODO: дёргать их так же часто как текущие не нужно, нужно реализовать ограничение
        self::getExtLostMeasure();

        $this->log('[' . self::LOG_ID . '] stop worker');
        ServiceRegister::addServiceRegister(ServiceRegister::SERVICE_IMPORT, ServiceRegister::TYPE_INFO,
            null, 'сервис закончил свою работу');
    }

    /**
     * @param $date
     * @throws Exception
     */
    private function getExtMeasuredValues($date)
    {
        $mcs = MeasureChannel::find()->where(['deleted' => false])->select(['uuid', 'param_id', 'type'])->asArray()->all();

        // список ID параметров которые будем тянуть из внешней базы
        $extIds = [];
        $mcUuids = [];
        foreach ($mcs as $mc) {
            $mcUuids[] = $mc['uuid'];
            $extIds[] = $mc['param_id'];
        }

        // вспомогательный запрос
        $subQuery = Measure::find()
            ->select(['measureChannelUuid', 'max(date) date1'])
            ->where(['measureChannelUuid' => $mcUuids])
            ->groupBy(['measureChannelUuid']);
        // выбираем из базы для каждого канала измерений последнее по дате значение
        $measures = Measure::find()
            ->innerJoin(['m' => $subQuery], 'measure.measureChannelUuid=m.measureChannelUuid and measure.date=m.date1')
            ->all();
        $res = [];
        foreach ($measures as $measure) {
            if (empty($res[$measure['measureChannelUuid']])) {
                $res[$measure['measureChannelUuid']] = $measure;
            }
        }

        $measures = &$res;

        // выбираем данные за указанную(текущую) дату
        $oracleDateFormat = 'YYYY.MM.DD HH24:MI:SS';
        $flows2 = Flows2::find()
            ->where(['ID' => $extIds])
            ->andWhere("TIME >= TO_TIMESTAMP(:startTime, '$oracleDateFormat')", [':startTime' => $date])
            ->andWhere("TIME < TO_TIMESTAMP(:endTime, '$oracleDateFormat')", [':endTime' => date('Y-m-d 00:00:00', strtotime($date . '+1 day'))])
            ->asArray()
            ->all();
        $flows2 = ArrayHelper::map($flows2, 'ID', function ($item) {
            return $item;
        });
        if (count($flows2) == 0) {
            ServiceRegister::addServiceRegister(ServiceRegister::SERVICE_IMPORT, ServiceRegister::TYPE_INFO,
                null, 'нет данных для импорта');
        }

        // тестовые данные
//        $flows2 = [
//            '5088' => [
//                'VALUE' => '5088.1',
//                'TIME' => '10.06.20 12:26:48,610000',
//            ],
//            '5086' => [
//                'VALUE' => '5086.1',
//                'TIME' => '12.06.20 11:34:51,052000',
//            ],
//            '5083' => [
//                'VALUE' => '5083.1',
//                'TIME' => '11.06.20 11:26:48,329000',
//            ],
//            '5087' => [
//                'VALUE' => '5087.1',
//                'TIME' => '11.06.20 11:26:48,735000',
//            ],
//            '5085' => [
//                'VALUE' => '5085.1',
//                'TIME' => '12.06.20 11:34:50,630000',
//            ],
//            '5084' => [
//                'VALUE' => '5084.1',
//                'TIME' => '10.06.20 12:26:48,063000',
//            ],
//        ];

        foreach ($mcs as $mc) {
            /** @var Measure $m */
            $m = null;
            $measureDateSrc = null;
            if (!empty($measures[$mc['uuid']])) {
                $m = $measures[$mc['uuid']];
                $measureDateSrc = $measures[$mc['uuid']]['date'];
            }

            // параметр из внешней базы
            $f2 = null;
            $extMeasureDateSrc = null;
            if (!empty($flows2[$mc['param_id']])) {
                $f2 = $flows2[$mc['param_id']];
                $extMeasureDateSrc = $flows2[$mc['param_id']]['TIME'];
            }

            switch ($mc['type']) {
                case MeasureType::MEASURE_TYPE_CURRENT:
//                    echo "MEASURE_TYPE_CURRENT" . PHP_EOL;
                    self::createMeasure($m, $f2, $mc['uuid']);
                    break;
                case MeasureType::MEASURE_TYPE_HOURS:
//                    echo "MEASURE_TYPE_HOURS" . PHP_EOL;
                    // проверяем на разность дат по часу
                    $measureDate = date('YmdH', strtotime($measureDateSrc));
                    $extMeasureDate = date('YmdH', strtotime(Flows2::getDatetime($extMeasureDateSrc)));
                    self::createMeasure($m, $f2, $mc['uuid'], $measureDate == $extMeasureDate);
                    break;
                case MeasureType::MEASURE_TYPE_DAYS:
//                    echo "MEASURE_TYPE_DAYS" . PHP_EOL;
                    // проверяем на разность дат по дню
                    $measureDate = date('Ymd', strtotime($measureDateSrc));
                    $extMeasureDate = date('Ymd', strtotime(Flows2::getDatetime($extMeasureDateSrc)));
                    self::createMeasure($m, $f2, $mc['uuid'], $measureDate == $extMeasureDate);
                    break;
                case MeasureType::MEASURE_TYPE_MONTH:
//                    echo "MEASURE_TYPE_MONTH" . PHP_EOL;
                    // проверяем на разность дат по месяцу
                    $measureDate = date('Ym', strtotime($measureDateSrc));
                    $extMeasureDate = date('Ym', strtotime(Flows2::getDatetime($extMeasureDateSrc)));
                    self::createMeasure($m, $f2, $mc['uuid'], $measureDate == $extMeasureDate);
                    break;
                default:
//                    echo "none" . PHP_EOL;
                    break;
            }
        }
    }

    /**
     * @param $measure Measure|null
     * @param $flows2 array of Flows2
     * @param $mcUuid string Measure channel uuid
     * @param bool $isSameDate Для текущих значений всегда true, для создания новой записи для другого часа, дня,
     *                          месяца false.
     * @throws Exception
     */
    private function createMeasure($measure, $flows2, $mcUuid, $isSameDate = true)
    {
        if (!empty($measure) && !empty($flows2)) {
            if ($isSameDate) {
                // обновляем запись с измерением
                $m = $measure;
            } else {
                // создаём запись с измерением
                $m = new Measure();
                $m->uuid = MainFunctions::GUID();
                $m->measureChannelUuid = $mcUuid;
            }
        } else if (empty($measure) && !empty($flows2)) {
            // создаём запись с измерением
            $m = new Measure();
            $m->uuid = MainFunctions::GUID();
            $m->measureChannelUuid = $mcUuid;
        } else {
            // нет записи с измерением, нет данных для создания записи
            return;
        }

        $m->value = floatval(Flows2::getFloatValue($flows2['VALUE']));
        $m->date = Flows2::getDatetime($flows2['TIME']);

        if (!$m->save()) {
            // TODO: запротоколировать ошибки записи
            ServiceRegister::addServiceRegister(ServiceRegister::SERVICE_IMPORT, ServiceRegister::TYPE_ERROR,
                null, 'ошибка: ' . json_encode($m->errors));
        }
    }

    /**
     * @throws InvalidConfigException
     * @throws Exception
     */
    private function getExtLostMeasure()
    {
        // TODO: проверку потерянных значений исправить с использованием типа измерения
        // TODO: то есть искать потерянные значения для дат, часов, дней, месяцев.

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
                    if (!$m->save()) {
                        // TODO: запротоколировать ошибки записи
                        ServiceRegister::addServiceRegister(ServiceRegister::SERVICE_IMPORT, ServiceRegister::TYPE_ERROR,
                            null, 'ошибка: ' . json_encode($m->errors));
                    }
                }
            }
        }
    }
}