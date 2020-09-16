<?php


namespace common\datasource;


use inpassor\daemon\Worker;

interface IDataSource
{
    /**
     * @param $date string Дата в формате 'Y-m-d H:i:s'
     */
    public function getData($date);

    /**
     * @param $date string Дата в формате 'Y-m-d H:i:s'
     */
    public function getLostData($date);

    /**
     * @param $worker Worker
     */
    public function setWorker(&$worker);
}