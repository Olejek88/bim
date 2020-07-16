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

    public static function tableName()
    {
        return 'table(PTER_LINK_API.GetFlow())';
    }

    public static function getTableSchema()
    {
        $sch = new TableSchema();
        $sch->name = self::tableName();
        $sch->fullName = self::tableName();
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
        $query = Yii::createObject(ActiveQuery::class, [self::class]);
        $query->sql = /** @lang oracle sql */
            'select * from table(PTER_LINK_API.GetFlow(:id))';
        return $query->params([':id' => $id])->one();
    }

    public function afterFind()
    {
        $pDate = date_parse_from_format('d.m.y H:i:s,v', $this->TIME);
        $date = date('Y-m-d H:i:s', mktime($pDate['hour'], $pDate['minute'], $pDate['second'], $pDate['month'], $pDate['day'], $pDate['year']));
        $this->TIME = $date;
        $this->setOldAttribute('TIME', $date);
    }

}