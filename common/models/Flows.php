<?php

namespace common\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\db\ColumnSchema;
use yii\db\Connection;
use yii\db\TableSchema;

/**
 * Class Flows
 *
 * Каналы измерений с приборов учёта из внешней системы.
 *
 * @property string PATH
 * @property string NAME
 * @property string ID
 */
class Flows extends ActiveRecord
{
    /**
     * @return object|Connection|null
     * @throws InvalidConfigException
     */
    public static function getDb()
    {
        return Yii::$app->get('table(PTER_LINK_API.GetFlows())');
    }

    public static function getTableSchema()
    {
        $sch = new TableSchema();
        $sch->name = self::tableName();
        $sch->fullName = self::tableName();
        $sch->primaryKey = ['ID'];
        $sch->columns = [
            'PATH' => new ColumnSchema(['type' => 'string', 'phpType' => 'string']),
            'NAME' => new ColumnSchema(['type' => 'string', 'phpType' => 'string']),
            'ID' => new ColumnSchema(['type' => 'integer', 'phpType' => 'integer']),
        ];
        return $sch;
    }
}