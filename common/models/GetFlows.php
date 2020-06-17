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
 * @property string ID
 */
class GetFlows extends ActiveRecord
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
        $path = new ColumnSchema(['type' => 'string', 'phpType' => 'string']);
        $name = new ColumnSchema(['type' => 'string', 'phpType' => 'string']);
        $id = new ColumnSchema(['type' => 'integer', 'phpType' => 'integer']);
        $sch->columns = [
            'PATH' => $path,
            'NAME' => $name,
            'ID' => $id,
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
            'select * from table(PTER_LINK_API.GetFlows())';
        return $query->params([]);
    }
}