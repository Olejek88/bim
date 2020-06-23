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
 * Последнее значение канала измерения с прибора учёта из внешней системы и время его последнего изменения.
 *
 * @property string TIME
 * @property string VALUE
 */
class Flow extends ActiveRecord
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
            'TIME' => new ColumnSchema(['type' => 'string', 'phpType' => 'string']),
            'VALUE' => new ColumnSchema(['type' => 'string', 'phpType' => 'string']),
        ];
        return $sch;
    }

    /**
     * @return void|ActiveRecord
     * @throws InvalidConfigException
     */
    public static function find()
    {
        throw new InvalidConfigException();
    }

    /**
     * @param $condition
     * @return void|ActiveRecord[]
     * @throws InvalidConfigException
     */
    public static function findAll($condition)
    {
        throw new InvalidConfigException();
    }


    /**
     * @param $id integer
     * @return ActiveRecord|null
     * @throws InvalidConfigException
     */
    public static function findOne($id)
    {
        $query = Yii::createObject(ActiveQuery::class, [Flows::class]);
        $query->sql = /** @lang oracle sql */
            'select * from table(PTER_LINK_API.GetFlows(:id))';
        return $query->params([':id' => $id])->one();
    }
}