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
 * Каналы измерений с приборов учёта из внешней системы с текущими значениями.
 *
 * @property string PATH
 * @property string NAME
 * @property integer ID
 * @property string FIXEDTYPE
 * @property string VALUE
 * @property string TIME
 * @property string WRITEABLE
 */
class Flows2 extends ActiveRecord
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
//            'select * from table(PTER_LINK_API.GetFlows2())';
        $tableName = 'get_flows2';
        $sch->name = $tableName;
        $sch->fullName = $tableName;
        $sch->primaryKey = [];
        $sch->columns = [
            'PATH' => new ColumnSchema(['type' => 'string', 'phpType' => 'string']),
            'NAME' => new ColumnSchema(['type' => 'string', 'phpType' => 'string']),
            'ID' => new ColumnSchema(['type' => 'integer', 'phpType' => 'integer']),
            'FIXEDTYPE' => new ColumnSchema(['type' => 'string', 'phpType' => 'string']),
            'VALUE' => new ColumnSchema(['type' => 'string', 'phpType' => 'string']),
            'TIME' => new ColumnSchema(['type' => 'string', 'phpType' => 'string']),
            'WRITEABLE' => new ColumnSchema(['type' => 'string', 'phpType' => 'string']),
        ];
        return $sch;
    }

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public static function find()
    {
        $query = Yii::createObject(ActiveQuery::class, [Flows2::class]);
        $query->sql = /** @lang oracle sql */
            'select * from table(PTER_LINK_API.GetFlows2())';
        return $query->params([]);
    }

    /**
     * @param $id integer
     * @return Flows2|null
     * @throws InvalidConfigException
     */
    public static function findOne($id)
    {
        $query = Yii::createObject(ActiveQuery::class, [Flows2::class]);
        $query->sql = /** @lang oracle sql */
            'select * from table(PTER_LINK_API.GetFlows2()) where ID=:id';
        return $query->params([':id' => $id])->one();
    }
}