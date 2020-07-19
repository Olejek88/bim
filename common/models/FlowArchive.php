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
 * Архивное значение канала измерения с прибора учёта из внешней системы, дата его изменения,
 * дата добавления архивного значения.
 *
 * @property string TIME
 * @property string VALUE
 * @property string ADDTIME
 */
class FlowArchive extends ActiveRecord
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

    public static function tableName()
    {
        // условие фильтрации по датам реально выглядит так - fromTime >= from and toTime < to
        // !!! ВАЖНО !!!! при фильтрации по датам, дата будет приведена  в формат 'Y-m-d H:i:s'
        return 'table(PTER_LINK_API.GetFlowArchive(:id,
         TO_TIMESTAMP(:fromTime, \'YYYY.MM.DD HH24:MI:SS\'), TO_TIMESTAMP(:toTime, \'YYYY.MM.DD HH24:MI:SS\')))';
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
            'ADDTIME' => new ColumnSchema(['type' => 'string', 'phpType' => 'string']),
        ];
        return $sch;
    }

    /**
     * @return object|ActiveQuery
     * @throws InvalidConfigException
     */
    public static function find()
    {
        return Yii::createObject(FlowArchiveQuery::class, [self::class]);
    }

    /**
     * @param $condition
     * @return ActiveRecord[]
     * @throws InvalidConfigException
     */
    public static function findAll($condition)
    {
        $query = Yii::createObject(FlowArchiveQuery::class, [self::class]);
        $query->where($condition);
        return $query->all();
    }


    /**
     * @param $id integer
     * @return ActiveRecord|null
     * @throws InvalidConfigException
     */
    public static function findOne($id)
    {
        $query = Yii::createObject(FlowArchiveQuery::class, [self::class]);
        $query->where(['ID' => $id])->orderBy(['TIME' => SORT_DESC])->limit(1);
        return $query->one();
    }

    public function afterFind()
    {
        $timeFields = ['TIME', 'ADDTIME'];
        foreach ($timeFields as $field) {
            $date = self::getDatetime($this->TIME);
            $this->$field = $date;
            $this->setOldAttribute($field, $date);
        }

        $value = self::getFloatValue($this->VALUE);
        $this->VALUE = $value;
        $this->setOldAttribute('VALUE', $value);
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