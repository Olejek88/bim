<?php

use common\models\EquipmentStatus;
use common\models\MeasureType;
use common\models\ObjectSubType;
use common\models\ObjectType;
use yii\db\Migration;

/**
 * Class m200607_070530_add_some_references
 */
class m200607_070530_add_some_references extends Migration
{
    const MEASURE_TYPE = '{{%measure_type}}';
    const PARAMETER_TYPE = '{{%parameter_type}}';
    const EQUIPMENT_TYPE = '{{%equipment_type}}';
    const EQUIPMENT_STATUS = '{{%equipment_status}}';
    const OBJECT_TYPE = '{{%object_type}}';
    const OBJECT_SUB_TYPE = '{{%object_sub_type}}';
    const ATTRIBUTE_TYPE = '{{%attribute_type}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        self::insertRefs(self::MEASURE_TYPE, 'Безразмерная', MeasureType::COMMON);
        self::insertRefs(self::MEASURE_TYPE, 'Частота', MeasureType::FREQUENCY);
        self::insertRefs(self::MEASURE_TYPE, 'Напряжение', MeasureType::VOLTAGE);
        self::insertRefs(self::MEASURE_TYPE, 'Давление', MeasureType::PRESSURE);
        self::insertRefs(self::MEASURE_TYPE, 'Мощность электроэнергии', MeasureType::POWER);
        self::insertRefs(self::MEASURE_TYPE, 'Потребленная электроэнергия', MeasureType::ENERGY);
        self::insertRefs(self::MEASURE_TYPE, 'Температура', MeasureType::TEMPERATURE);
        self::insertRefs(self::MEASURE_TYPE, 'Ток', MeasureType::CURRENT);
        self::insertRefs(self::MEASURE_TYPE, 'Статус', MeasureType::STATUS);
        self::insertRefs(self::MEASURE_TYPE, 'Уровень сигнала', MeasureType::RSSI);
        self::insertRefs(self::MEASURE_TYPE, 'Потребленная тепловая энергия', MeasureType::HEAT_CONSUMED);
        self::insertRefs(self::MEASURE_TYPE, 'Тепловая энергия на подаче', MeasureType::HEAT_IN);
        self::insertRefs(self::MEASURE_TYPE, 'Тепловая энергия на обратке', MeasureType::HEAT_OUT);
        self::insertRefs(self::MEASURE_TYPE, 'Потребление холодной воды', MeasureType::COLD_WATER);

        self::insertRefs(self::OBJECT_SUB_TYPE, 'Общий', ObjectSubType::GENERAL);
        self::insertRefs(self::OBJECT_SUB_TYPE, 'МКД', ObjectSubType::MKD);
        self::insertRefs(self::OBJECT_SUB_TYPE, 'Коммерческий', ObjectSubType::COMMERCE);
        self::insertRefs(self::OBJECT_SUB_TYPE, 'Размещение', ObjectSubType::PLACEMENT);

        self::insertRefs(self::OBJECT_TYPE, 'Регион', ObjectType::REGION);
        self::insertRefs(self::OBJECT_TYPE, 'Район', ObjectType::DISTRICT);
        self::insertRefs(self::OBJECT_TYPE, 'Город', ObjectType::CITY);
        self::insertRefs(self::OBJECT_TYPE, 'Район города', ObjectType::CITY_DISTRICT);
        self::insertRefs(self::OBJECT_TYPE, 'Улица', ObjectType::STREET);
        self::insertRefs(self::OBJECT_TYPE, 'Подрайон', ObjectType::SUB_DISTRICT);

        //self::insertRefs(self::EQUIPMENT_TYPE, 'Счетчик холодной воды', EquipmentType::COLD_WATER_COUNTER);
        //self::insertRefs(self::EQUIPMENT_TYPE, 'Счетчик горячей воды', EquipmentType::HOT_WATER_COUNTER);
        //self::insertRefs(self::EQUIPMENT_TYPE, 'Счетчик электроэнергии', EquipmentType::ENERGY_COUNTER);
        //self::insertRefs(self::EQUIPMENT_TYPE, 'Теплосчетчик', EquipmentType::HEAT_COUNTER);

        self::insertRefs(self::EQUIPMENT_STATUS, 'Все в порядке', EquipmentStatus::WORK);
        self::insertRefs(self::EQUIPMENT_STATUS, 'Нет связи', EquipmentStatus::NO_CONNECT);
        self::insertRefs(self::EQUIPMENT_STATUS, 'Не работает', EquipmentStatus::NOT_WORK);
        self::insertRefs(self::EQUIPMENT_STATUS, 'Неизвестен', EquipmentStatus::UNKNOWN);
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
        echo "m200607_070530_add_some_references cannot be reverted.\n";

        return false;
    }
}
