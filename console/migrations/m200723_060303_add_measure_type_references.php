<?php

use common\models\MeasureType;
use yii\db\Migration;

/**
 * Class m200723_060303_add_measure_type_references
 */
class m200723_060303_add_measure_type_references extends Migration
{
    const MEASURE_TYPE = '{{%measure_type}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        self::insertRefs(self::MEASURE_TYPE, 'Авария', MeasureType::ALARM);
        self::insertRefs(self::MEASURE_TYPE, 'Перепад давления', MeasureType::PRESSURE_DIFF);
        self::insertRefs(self::MEASURE_TYPE, 'Время', MeasureType::TIME);
        self::insertRefs(self::MEASURE_TYPE, 'Температура ГВС', MeasureType::TEMPERATURE_HOT_WATER);
        self::insertRefs(self::MEASURE_TYPE, 'Наработка', MeasureType::WORKING_TIME);
        self::insertRefs(self::MEASURE_TYPE, 'Расход теплоносителя', MeasureType::HEAT_FLOW);
        return true;
    }

    private function insertRefs($table, $title, $uuid)
    {
        $date = date('Y-m-d\TH:i:s');
        $this->insert($table, [
            'uuid' => $uuid,
            'title' => $title,
            'createdAt' => $date,
            'changedAt' => $date,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200723_060303_add_measure_type_references cannot be reverted.\n";

        return false;
    }
}
