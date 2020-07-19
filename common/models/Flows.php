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
 * Каналы измерений с приборов учёта из внешней системы.
 *
 * @property string PATH
 * @property string NAME
 * @property string ID
 * @property-read array $permissions
 */
class Flows extends ActiveRecord
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
        return 'table(PTER_LINK_API.GetFlows())';
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
        ];
        return $sch;
    }

    /**
     * @return array
     */
    public function getPermissions()
    {
        $class = explode('\\', get_class($this));
        $class = $class[count($class) - 1];

        return [
            'tree' => ['name' => 'tree' . $class, 'description' => 'Дерево параметров внешней базы'],
            'link-obj-form' => ['name' => 'link-obj-form' . $class, 'description' => 'Создание связи параметров из внешеней базы с объектами'],
        ];
    }
}