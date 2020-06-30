<?php

namespace common\models;

use DateTime;
use yii\db\ActiveRecord;

/** @noinspection UndetectableTableInspection */

/**
 * Возвращает список имён разрешений.
 *
 * @property array $permissions
 */
class PoliterModel extends ActiveRecord
{
    public static function getSetTimeZoneHandler()
    {
        $setTimeZone = function ($event) {
            $date = new DateTime();
            $offset = $date->getOffset();
            $sign = $offset < 0 ? '-' : '+';
            $offset = abs($offset);
            $hour = intval($offset / (60 * 60));
            $min = abs(abs($offset) - abs($hour) * (60 * 60)) / 60;
            $tzFinal = $sign . $hour . ':' . $min;
            $event->sender->createCommand("SET time_zone='" . $tzFinal . "';")->execute();
        };

        return $setTimeZone;
    }

    /**
     * @return array
     */
    public function getPermissions()
    {

        $class = explode('\\', get_class($this));
        $class = $class[count($class) - 1];

        return [
            'new' => ['name' => 'create' . $class, 'description' => 'Создание'],
            'index' => ['name' => 'index' . $class, 'description' => 'Просмотр списка'],
            'tree' => ['name' => 'tree' . $class, 'description' => 'Дерево'],
            'save' => ['name' => 'view' . $class, 'description' => 'Сохранение'],
            'delete' => ['name' => 'delete' . $class, 'description' => 'Удаление'],
        ];
    }
}