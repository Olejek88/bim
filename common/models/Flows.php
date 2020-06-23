<?php

namespace common\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
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
        return Yii::$app->get('oracle');
    }

    public static function getTableSchema()
    {
        $sch = new TableSchema();
        // типа имя таблицы
//        $tableName = /** @lang oracle sql */
//            'select * from table(PTER_LINK_API.GetFlows())';
        $tableName = 'get_flows';
        $sch->name = $tableName;
        $sch->fullName = $tableName;
        $sch->primaryKey = [];
        $sch->columns = [
            'PATH' => new ColumnSchema(['type' => 'string', 'phpType' => 'string']),
            'NAME' => new ColumnSchema(['type' => 'string', 'phpType' => 'string']),
            'ID' => new ColumnSchema(['type' => 'integer', 'phpType' => 'integer']),
        ];
        return $sch;
    }

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public static function find()
    {
        $query = Yii::createObject(ActiveQuery::class, [Flows::class]);
        $query->sql = /** @lang oracle sql */
            'select * from table(PTER_LINK_API.GetFlows())';
        return $query->params([]);
    }

    /**
     * @param $id integer
     * @return Flows|null
     * @throws InvalidConfigException
     */
    public static function findOne($id)
    {
        $query = Yii::createObject(ActiveQuery::class, [Flows::class]);
        $query->sql = /** @lang oracle sql */
            'select * from table(PTER_LINK_API.GetFlows(:id))';
        return $query->params([':id' => $id])->one();
    }

    public static function findAll($condition)
    {
        $query = Yii::createObject(ActiveQuery::class, [Flows::class]);
        $query->sql = /** @lang oracle sql */
            'select * from table(PTER_LINK_API.GetFlows())';
        return $query->params([]);
    }

}