<?php


namespace common\datasource\vega\models;

/**
 * Класс для задания штатными средствами прав доступа для контроллера DefaultController в модуле
 * @package common\datasource\politer\models
 */
class VegaController
{
    /**
     * @return array
     */
    public function getPermissions()
    {
        return [
            'index' => ['name' => 'indexVega', 'description' => 'Список доступных параметров внешней базы vega'],
            'table' => ['name' => 'tableVega', 'description' => 'Таблица доступных параметров внешней базы vega'],
        ];
    }
}