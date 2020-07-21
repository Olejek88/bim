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
 * Каналы измерений с приборов учёта из внешней системы с текущими значениями.
 *
 * @property string PATH
 * @property string NAME
 * @property integer ID
 * @property string FIXEDTYPE
 * @property string VALUE
 * @property string TIME
 * @property string WRITABLE
 *
 * @property-read array $permissions
 */
class Flows2 extends ActiveRecord
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
        $sch->primaryKey = ['ID'];
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

    public static function tableName()
    {
        return 'table(PTER_LINK_API.GetFlows2())';
    }

//    /**
//     * @return object|ActiveQuery
//     * @throws InvalidConfigException
//     */
//    public static function find()
//    {
//        return Yii::createObject(Flows2Query::class, [self::class]);
//    }

    public function afterFind()
    {
        $date = self::getDatetime($this->TIME);
        $this->TIME = $date;
        $this->setOldAttribute('TIME', $date);
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