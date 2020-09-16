<?php


namespace common\models;

class DataSource
{
    /**
     * @return array
     */
    public function getPermissions()
    {
        $class = explode('\\', get_class($this));
        $class = $class[count($class) - 1];

        return [
            'index' => ['name' => 'index' . $class, 'description' => 'Просмотр списка источников данных'],
        ];
    }
}