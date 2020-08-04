<?php


namespace common\datasource\politer\models;

/**
 * Класс для задания штатными средствами прав доступа для контроллера DefaultController в модуле
 * @package common\datasource\politer\models
 */
class DefaultController
{
    /**
     * @return array
     */
    public function getPermissions()
    {
        return [
            'index' => ['name' => 'indexDefault', 'description' => 'Список доступных параметров внешней базы'],
            'link-obj-form' => ['name' => 'link-obj-formDefault', 'description' => 'Создание связи параметров из внешеней базы с объектами'],
        ];
    }
}