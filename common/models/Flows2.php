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
 */
class Flows2 extends ActiveRecord
{
    /**
     * @return object|Connection|null
     * @throws InvalidConfigException
     */
    public static function getDb()
    {
        return Yii::$app->get('table(PTER_LINK_API.GetFlows2())');
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

    public function afterFind()
    {
        $pDate = date_parse_from_format('d.m.y H:i:s,v', $this->TIME);
        $date = date('Y-m-d H:i:s', mktime($pDate['hour'], $pDate['minute'], $pDate['second'], $pDate['month'], $pDate['day'], $pDate['year']));
        $this->TIME = $date;
        $this->setOldAttribute('TIME', $date);
    }
}