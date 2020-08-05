<?php

namespace common\datasource;

trait DataSourceTrait
{
    public $description;
    private $worker;

    /**
     * @param $message string
     */
    public function log($message)
    {
        if ($this->worker != null) {
            $this->worker->log('[' . $this->id . '] ' . $message);
        }
    }
}