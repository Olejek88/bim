<?php

namespace console\workers;

use common\components\MainFunctions;
use common\datasource\DataSourceTrait;
use common\datasource\IDataSource;
use common\models\ServiceRegister;
use Exception;
use inpassor\daemon\Worker;
use Yii;
use yii\base\Module;

class PoliterService extends Worker
{
    const LOG_ID = "data gathering";

    private $checkLostDate = [];

    public function init()
    {
        $this->logFile = '@console/runtime/daemon/logs/worker-politer.log';
        $this->errorLogFile = '@console/runtime/daemon/logs/worker-politer-error.log';
        parent::init();
    }

    /**
     * @throws Exception
     */
    public function run()
    {
        $this->log('[' . self::LOG_ID . '] start worker');

        // тянем текущие значения
        $currentDate = date('Y-m-d H:i:s');

        $modules = Yii::$app->getModules();
        foreach ($modules as $prefix => $module) {
            /** @var $module IDataSource|DataSourceTrait|Module */
            if (is_array($module) && !empty($module['class']) && str_contains($module['class'], 'common\datasource\\')) {
                $module = Yii::$app->getModule($prefix);
            } else if (is_object($module) && str_contains($module::className(), 'common\datasource\\')) {
            } else {
                continue;
            }

            // передаём в модуль worker для возможности протоколирования
            $module->setWorker($this);
            if (empty($this->checkLostDate[$prefix])) {
                $hour = date('G');
                $checkDate = date('Y-m-d 03:00:00');
                if ($hour >= 0 && $hour < 4) {
                    // проверяем сегодня
                    $this->checkLostDate[$prefix] = strtotime($checkDate);
                } else {
                    // проверяем завтра
                    $this->checkLostDate[$prefix] = strtotime($checkDate . ' +1 day');
                }
            }

            $this->log('[' . $prefix . '] get data from "' . $module->description);
            $module->getData($currentDate);
            // пытаемся получить измерения не полученные ранее
            if (time() > $this->checkLostDate[$prefix]) {
                $module->getLostData($currentDate);
                $this->checkLostDate[$prefix] = strtotime(date('Y-m-d 03:00:00') . ' +1 day');
            }
        }

        // обновляем таблицу с последними измеренными значениями
        MainFunctions::updateMeasureLast();

        $this->log('[' . self::LOG_ID . '] stop worker');
        ServiceRegister::addServiceRegister(ServiceRegister::SERVICE_IMPORT, ServiceRegister::TYPE_INFO,
            null, 'сервис закончил свою работу');
    }
}