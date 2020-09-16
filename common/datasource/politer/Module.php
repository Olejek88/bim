<?php


namespace common\datasource\politer;


use common\components\MainFunctions;
use common\datasource\DataSourceTrait;
use common\datasource\IDataSource;
use common\datasource\politer\models\FlowArchive;
use common\datasource\politer\models\Flows2;
use common\models\Measure;
use common\models\MeasureChannel;
use common\models\MeasureType;
use Exception;
use inpassor\daemon\Worker;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

class Module extends \yii\base\Module implements IDataSource
{
    use DataSourceTrait;

    public $oracle;

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if ($this->description == null) {
            $this->description = "Politer ({$this->id})";
        }

        $this->oracle = Yii::createObject($this->oracle);
        Yii::$app->set('politerDb', $this->oracle);
    }

    /**
     * @param Worker $worker
     */
    public function setWorker(&$worker)
    {
        $this->worker = $worker;
    }

    /**
     * @param $date string
     * @throws Exception
     */
    public function getData($date)
    {
        // принудительно устанавливаем свою базу данных для модуля
        Yii::$app->set('politerDb', $this->oracle);

        $mcs = MeasureChannel::find()->where(['deleted' => false, 'data_source' => $this->id])
            ->select(['uuid', 'param_id', 'type'])->asArray()->all();

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
            ->andWhere("TIME < TO_TIMESTAMP(:endTime, '$oracleDateFormat')", [
                ':endTime' => date('Y-m-d 00:00:00', strtotime($date . '+1 day'))
            ])
            ->asArray()
            ->all();
        $flows2 = ArrayHelper::map($flows2, 'ID', function ($item) {
            return $item;
        });

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
                case MeasureType::MEASURE_TYPE_TOTAL:
//                    echo "MEASURE_TYPE_TOTAL" . PHP_EOL;
                    // проверяем на разность дат по дню
                    $measureDate = date('Ymd', strtotime($measureDateSrc));
                    $extMeasureDate = date('Ymd', strtotime(Flows2::getDatetime($extMeasureDateSrc)));
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
     * @param $flows2 Flows2
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
            $action = $isSameDate == true ? 'обновлении' : 'создании';
            $message = "Ошибки при $action измерения(param_id={$flows2['ID']}): ";
            foreach ($m->errors as $key => $error) {
                $message .= $error[0] . ',';
            }

            self::log($message);
        }
    }

    /**
     * @param $date string
     * @throws InvalidConfigException
     */
    public function getLostData($date)
    {
        // принудительно устанавливаем свою базу данных для модуля
        Yii::$app->set('politerDb', $this->oracle);

        // как глубоко будем проверять пропущенные значения
        $deep = -7;

        // проверяем каналы с частотой текущее значение
        self::getLostByHours(MeasureType::MEASURE_TYPE_CURRENT, $date, 'Y-m-d H:00:00', $deep);

        // проверяем каналы с частотой в час
        self::getLostByHours(MeasureType::MEASURE_TYPE_HOURS, $date, 'Y-m-d H:00:00', $deep);

        // проверяем каналы с частотой в день
        self::getLostByDays(MeasureType::MEASURE_TYPE_DAYS, $date, 'Y-m-d 00:00:00', $deep);

        // проверяем каналы с частотой сумма
        self::getLostByDays(MeasureType::MEASURE_TYPE_TOTAL, $date, 'Y-m-d 00:00:00', $deep);
    }

    /**
     * @param $type
     * @param $date
     * @param $dateFormat
     * @param int $deep
     * @throws InvalidConfigException
     */
    private function getLostByHours($type, $date, $dateFormat, $deep = -7)
    {
        $mcs = self::getMeasureChannels($type);

        // пустые списки измерений по каналам
        $measuresByChannels = ArrayHelper::map($mcs, 'uuid', function ($item) {
            return [];
        });

        $measures = self::getMeasures($type, $date, $dateFormat, $deep);
        foreach ($measures as $measure) {
            $measuresByChannels[$measure['measureChannelUuid']][$measure['uuid']] = $measure;
        }

        $measures = &$measuresByChannels;
        self::getLostMeasuresByHours($measures, $date, $mcs, $deep);
    }

    /**
     * @param $type
     * @return array of MeasureChannel arrays
     */
    private function getMeasureChannels($type)
    {
        $mcs = MeasureChannel::find()->where(['deleted' => false, 'type' => $type, 'data_source' => $this->id])
            ->select(['uuid', 'param_id', 'type'])->asArray()->all();
        $mcs = ArrayHelper::map($mcs, 'uuid', function ($item) {
            return $item;
        });
        return $mcs;
    }

    /**
     * @param $type
     * @param $date
     * @param $dateFormat
     * @param int $deep
     * @return Measure[]
     */
    private function getMeasures($type, $date, $dateFormat, $deep = -7)
    {
        $sq = MeasureChannel::find()
            ->where(['deleted' => false, 'type' => $type, 'data_source' => $this->id])
            ->select(['uuid']);
        return Measure::find()->where(['measureChannelUuid' => $sq])
            ->andWhere([
                'AND',
                ['>=', 'date', date($dateFormat, strtotime($date . '' . $deep . ' day'))],
                ['<', 'date', date($dateFormat, strtotime($date))],
            ])
            ->asArray()
            ->orderBy(['date' => SORT_ASC])
            ->all();
    }

    /**
     * @param $measures
     * @param $date
     * @param $mcs
     * @param int $deep
     * @throws InvalidConfigException
     * @throws Exception
     */
    private function getLostMeasuresByHours(&$measures, $date, &$mcs, $deep = -7)
    {
        foreach ($measures as $channelUuid => $channel) {
            $absDeep = abs(intval($deep)) * 24;
            $dateList = [];
            for ($i = 1; $i <= $absDeep; $i++) {
                $dateList[date('Y-m-d H:00:00', strtotime($date . '-' . $i . ' hour'))] = null;
            }

            foreach ($channel as $measure) {
                $measureDate = date('Y-m-d H:00:00', strtotime($measure['date']));
                unset($dateList[$measureDate]);
            }

            foreach ($dateList as $lostDate => $value) {
                $archive = FlowArchive::find()
                    ->where(['ID' => $mcs[$channelUuid]['param_id'],
                        'fromTime' => date('Y-m-d H:00:00', strtotime($lostDate)),
                        'toTime' => date('Y-m-d H:00:00', strtotime($lostDate . '+1 hour')),
                    ])->one();
                if ($archive) {
                    $m = new Measure();
                    $m->uuid = MainFunctions::GUID();
                    $m->measureChannelUuid = $channelUuid;
                    $m->date = $archive->TIME;
                    $m->value = $archive->VALUE;
                    if (!$m->save()) {
                        $message = "Ошибки при создании пропущенного измерения(param_id={$archive['ID']}): ";
                        foreach ($m->errors as $key => $error) {
                            $message .= $error[0] . ',';
                        }

                        self::log($message);
                    }
                }
            }
        }
    }

    /**
     * @param $type
     * @param $date
     * @param $dateFormat
     * @param int $deep
     * @throws InvalidConfigException
     */
    private function getLostByDays($type, $date, $dateFormat, $deep = -7)
    {
        $mcs = self::getMeasureChannels($type);

        // пустые списки измерений по каналам
        $measuresByChannels = ArrayHelper::map($mcs, 'uuid', function ($item) {
            return [];
        });

        $measures = self::getMeasures($type, $date, $dateFormat, $deep);
        foreach ($measures as $measure) {
            $measuresByChannels[$measure['measureChannelUuid']][$measure['uuid']] = $measure;
        }

        $measures = &$measuresByChannels;
        self::getLostMeasuresByDays($measures, $date, $mcs, $deep);
    }

    /**
     * @param $measures
     * @param $date
     * @param $mcs
     * @param int $deep
     * @throws InvalidConfigException
     * @throws Exception
     */
    private function getLostMeasuresByDays(&$measures, $date, &$mcs, $deep = -7)
    {
        foreach ($measures as $channelUuid => $channel) {
            $absDeep = abs(intval($deep));
            $dateList = [];
            for ($i = 1; $i <= $absDeep; $i++) {
                $dateList[date('Y-m-d 00:00:00', strtotime($date . '-' . $i . ' day'))] = null;
            }

            foreach ($channel as $measure) {
                $measureDate = date('Y-m-d 00:00:00', strtotime($measure['date']));
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
                    $m->date = $archive->TIME;
                    $m->value = $archive->VALUE;
                    if (!$m->save()) {
                        $message = "Ошибки при создании пропущенного измерения(param_id={$archive['ID']}): ";
                        foreach ($m->errors as $key => $error) {
                            $message .= $error[0] . ',';
                        }

                        self::log($message);
                    }
                }
            }
        }
    }
}