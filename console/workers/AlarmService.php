<?php

namespace console\workers;

use common\components\MainFunctions;
use common\models\Alarm;
use common\models\Measure;
use common\models\MeasureChannel;
use common\models\MeasureType;
use common\models\Register;
use common\models\ServiceRegister;
use Exception;
use inpassor\daemon\Worker;

class AlarmService extends Worker
{
    const LOG_ID = "alarm";

    public function init()
    {
        $this->logFile = '@console/runtime/daemon/logs/worker-alarm.log';
        $this->errorLogFile = '@console/runtime/daemon/logs/worker-alarm-error.log';
        parent::init();
    }

    /**
     * @throws Exception
     */
    public function run()
    {
        $this->log('[' . self::LOG_ID . '] start worker');

        self::checkData();
        self::checkAlarm();

        $this->log('[' . self::LOG_ID . '] stop worker');
        ServiceRegister::addServiceRegister(ServiceRegister::SERVICE_ARCHIVE, ServiceRegister::TYPE_INFO,
            null, 'сервис закончил свою работу');
    }

    /**
     * @throws Exception
     */
    function checkData()
    {
        $channels = MeasureChannel::find()->all();
        foreach ($channels as $channel) {
            $last = Measure::find()
                ->where(['measureChannelId' => $channel['_id']])
                ->orderBy('date desc')
//                ->limit(1)
                ->one();
            if ($last) {
                if (time() - strtotime($last['date']) > 3600 * 24 * 7) {
                    if ($channel['status'] == MeasureChannel::STATUS_ACTIVE) {
                        $channel['status'] = MeasureChannel::STATUS_NO_CONNECT;
                        $channel->save();
                        $this->log('[' . self::LOG_ID . '] ' . $channel['title'] . ' change status to no connect ' . time() . ' ' . strtotime($last['date']));
                        Register::addRegister('Статус канала изменен на нет связи', Register::TYPE_ERROR, $channel['uuid']);
                        ServiceRegister::addServiceRegister(ServiceRegister::SERVICE_ALARM, ServiceRegister::TYPE_INFO,
                            $channel['uuid'],
                            'канал ' . $channel->object->getFullTitle() . ' ' . $channel->title . ' сменил статус на нет связи');
                    }
                } else {
                    if ($channel['status'] == MeasureChannel::STATUS_NO_CONNECT) {
                        $channel['status'] = MeasureChannel::STATUS_ACTIVE;
                        $channel->save();
                        $this->log('[' . self::LOG_ID . '] ' . $channel['title'] . ' change status to ok');
                        Register::addRegister('Статус канала изменен на на связи', Register::TYPE_INFO, $channel['uuid']);
                    }
                }
            } else {
                // нет данных совсем - канал выключен
                $channel->status = MeasureChannel::STATUS_OFF;
                $channel->save();
            }
        }
    }

    /**
     * @throws Exception
     */
    function checkAlarm()
    {
        $channels = MeasureChannel::find()
            ->where(['<>', 'status', MeasureChannel::STATUS_OFF])
            ->andWhere(['measureTypeUuid' => MeasureType::ALARM])
            ->all();
        foreach ($channels as $channel) {
            // вообще оно там одно будет скорее всего
            $last = Measure::find()
                ->where(['measureChannelId' => $channel['_id']])
                ->orderBy('date desc')
//                ->limit(1)
                ->one();
            if ($last && $last['value'] == 1) {
                $alarm = Alarm::find()
                    ->where(['entityUuid' => $channel['uuid']])
                    ->andWhere(['type' => Alarm::TYPE_ALARM])
                    //->andWhere(['status' => Alarm::STATUS_ACTIVE])
                    ->one();
                if ($alarm) {
                    if ($alarm['active'] != Alarm::STATUS_ACTIVE) {
                        $alarm['active'] = Alarm::STATUS_ACTIVE;
                        $alarm->save();
                        Register::addRegister($channel['title'], Register::TYPE_WARNING, $channel->object->uuid);
                    }
                } else {
                    $alarm = new Alarm();
                    $alarm->uuid = MainFunctions::GUID();
                    $alarm->status = Alarm::STATUS_ACTIVE;
                    $alarm->title = $channel['title'];
                    $alarm->type = Alarm::TYPE_ALARM;
                    $alarm->level = Alarm::LEVEL_PROBLEM;
                    $alarm->entityUuid = $channel->object->uuid;
                    $alarm->save();
                }
            }
        }
    }
}