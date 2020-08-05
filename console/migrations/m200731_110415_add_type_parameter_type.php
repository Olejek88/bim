<?php

use common\models\Objects;
use common\models\ObjectType;
use common\models\ParameterType;
use yii\db\Migration;

/**
 * Class m200731_110415_add_type_parameter_type
 */
class m200731_110415_add_type_parameter_type extends Migration
{
    const PARAMETER_TYPE = '{{%parameter_type}}';

    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function safeUp()
    {
        //$this->addColumn(self::PARAMETER_TYPE, 'type', $this->integer()->defaultValue(0));
        self::insertRefs(self::PARAMETER_TYPE, 'Площадь помещений', ParameterType::SQUARE, 1);
        self::insertRefs(self::PARAMETER_TYPE, 'Объем помещений', ParameterType::VOLUME, 1);
        self::insertRefs(self::PARAMETER_TYPE, 'Средняя ширина стен', ParameterType::WALL_WIDTH, 1);
        self::insertRefs(self::PARAMETER_TYPE, 'Коэффициент теплопроводности стен', ParameterType::KNT_HEAT_CONDUCT, 1);
        self::insertRefs(self::PARAMETER_TYPE, 'Количество этажей', ParameterType::STAGE_COUNT, 1);
        self::insertRefs(self::PARAMETER_TYPE, 'Коэффициент теплопроводности крыши', ParameterType::KNT_ROOF, 1);
        self::insertRefs(self::PARAMETER_TYPE, 'Среднее расчетное потребление тепла на м2', ParameterType::SUM_AVG_HEAT, 1);
        self::insertRefs(self::PARAMETER_TYPE, 'Количество персонала/жителей', ParameterType::PERSONAL_CNT, 1);
        /** @var Objects [] $allObjects */
        $allObjects = Objects::find()->where(['objectTypeUuid' => ObjectType::OBJECT])->all();
        foreach ($allObjects as $object) {
            $square = rand(150, 15000);
            $object->checkCreateParameter(ParameterType::SQUARE, $square);
            $object->checkCreateParameter(ParameterType::VOLUME, $square * 3);
            $object->checkCreateParameter(ParameterType::WALL_WIDTH, 0.5);
            $object->checkCreateParameter(ParameterType::KNT_HEAT_CONDUCT, rand(14, 90) / 100);
            $object->checkCreateParameter(ParameterType::STAGE_COUNT, rand(1, 10));
            $object->checkCreateParameter(ParameterType::KNT_ROOF, 0.4);
            $object->checkCreateParameter(ParameterType::PERSONAL_CNT, rand(4, 900));
        }
    }

    private function insertRefs($table, $title, $uuid, $type)
    {
        $date = date('Y-m-d\TH:i:s');
        $this->insert($table, [
            'uuid' => $uuid,
            'title' => $title,
            'type' => $type,
            'createdAt' => $date,
            'changedAt' => $date,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200731_110415_add_type_parameter_type cannot be reverted.\n";

        return false;
    }
}
