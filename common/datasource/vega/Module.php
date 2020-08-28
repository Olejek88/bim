<?php


namespace common\datasource\vega;


use common\datasource\DataSourceTrait;
use common\datasource\IDataSource;
use inpassor\daemon\Worker;

class Module extends \yii\base\Module implements IDataSource
{
    use DataSourceTrait;

    public $server;

    /**
     *
     */
    public function init()
    {
        parent::init();
        if ($this->description == null) {
            $this->description = "Vega ({$this->id})";
        }

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
     *
     */
    public function getData($date)
    {
    }


    /**
     * @param $date string
     *
     */
    public function getLostData($date)
    {
    }

}