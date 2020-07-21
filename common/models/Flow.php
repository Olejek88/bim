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
    use PoliterTrait;

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
        $sch->name = self::tableName();
        $sch->fullName = self::tableName();
        $sch->primaryKey = [];
        $sch->columns = [
            'TIME' => new ColumnSchema(['type' => 'string', 'phpType' => 'string']),
            'VALUE' => new ColumnSchema(['type' => 'string', 'phpType' => 'string']),
        ];
        return $sch;
    }

    public static function tableName()
    {
        return 'table(PTER_LINK_API.GetFlow())';
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
        $date = self::getDatetime($this->TIME);
        $this->TIME = $date;
        $this->setOldAttribute('TIME', $date);
    }

    /**
     * @return array
     */
    public function getPermissions()
    {
        $class = explode('\\', get_class($this));
        $class = $class[count($class) - 1];

        return [];
    }
}