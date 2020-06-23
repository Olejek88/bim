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
 * Class GetFlows
 *
 * @property string PATH
 * @property string NAME
 * @property integer ID
 * @property string FIXEDTYPE
 * @property string VALUE
 * @property string TIME
 * @property string WRITEABLE
 */
class GetFlows2 extends ActiveRecord
{
    /**
     * @return Objects|Connection|null
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
            'FIXEDTYPE' => new ColumnSchema(['type' => 'integer', 'phpType' => 'integer']),
            'VALUE' => new ColumnSchema(['type' => 'integer', 'phpType' => 'integer']),
            'TIME' => new ColumnSchema(['type' => 'integer', 'phpType' => 'integer']),
            'WRITEABLE' => new ColumnSchema(['type' => 'integer', 'phpType' => 'integer']),
        ];
        return $sch;
    }

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public static function find()
    {
        $query = Yii::createObject(ActiveQuery::class, [GetFlows::class]);
        $query->sql = /** @lang oracle sql */
            'select * from table(PTER_LINK_API.GetFlows2())';
        return $query->params([]);
    }
}