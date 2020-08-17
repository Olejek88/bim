<?php

use common\models\Objects;
use common\models\ObjectType;
use common\models\ParameterType;
use yii\db\Migration;

/**
 * Class m200817_063306_add_fields_for_calculates
 */
class m200817_063306_add_fields_for_calculates extends Migration
{
    const EVENT_TYPE = '{{%event_type}}';
    const EVENT = '{{%event}}';
    const PARAMETER_TYPE = '{{%parameter_type}}';

    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function safeUp()
    {
        $this->addColumn(self::EVENT_TYPE, 'cnt_effect', $this->float()->defaultValue(0.1));
        $this->addColumn(self::EVENT_TYPE, 'source', $this->integer()->defaultValue(0));
        $this->addColumn(self::EVENT_TYPE, 'parameterTypeUuid', $this->string(36));
        $this->addColumn(self::EVENT, 'cnt_coverage', $this->float()->defaultValue(0.1));

        self::insertRefs(self::PARAMETER_TYPE, 'Утепленность дверных проемов входных групп', ParameterType::DOOR_EFFICIENCY, 2);
        self::insertRefs(self::PARAMETER_TYPE, 'Наличие регуляторов отопления', ParameterType::HEAT_REGULATOR, 2);
        self::insertRefs(self::PARAMETER_TYPE, 'Утепленность пола', ParameterType::FLOOR_EFFICIENCY, 2);
        self::insertRefs(self::PARAMETER_TYPE, 'Оснащенность узлами АИТП', ParameterType::AITP, 2);
        self::insertRefs(self::PARAMETER_TYPE, 'Герметичность межпанельных стыков', ParameterType::PANEL, 2);
        self::insertRefs(self::PARAMETER_TYPE, 'Утепленность чердаков и подвалов', ParameterType::BASEMENT_EFFICIENCY, 2);
        self::insertRefs(self::PARAMETER_TYPE, 'Экономичность светильников наружного освещения', ParameterType::OUTDOOR_LIGHT, 2);
        self::insertRefs(self::PARAMETER_TYPE, 'Экономичность светильников внутреннего освещения', ParameterType::INDOOR_LIGHT, 2);
        self::insertRefs(self::PARAMETER_TYPE, 'Охват установки датчиков движения на освещение', ParameterType::MOVEMENT_LIGHT, 2);
        self::insertRefs(self::PARAMETER_TYPE, 'Энергосберегающие окна', ParameterType::WINDOWS_EFFICIENCY, 2);
        self::insertRefs(self::PARAMETER_TYPE, 'Трубы отопления и радиаторы', ParameterType::PIPES_RADIATORS, 2);
        self::insertRefs(self::PARAMETER_TYPE, 'Эффективность котла / другого источника отопления', ParameterType::HEAT_EFFICIENCY, 2);
        self::insertRefs(self::PARAMETER_TYPE, 'Утепленность ограждающих конструкций', ParameterType::WALL_EFFICIENCY, 2);
        self::insertRefs(self::PARAMETER_TYPE, 'Эффективность котельной', ParameterType::BOILER_EFFICIENCY, 2);
        self::insertRefs(self::PARAMETER_TYPE, 'Эффективность системы теплогенерации', ParameterType::HEAT_GENERATION_EFFICIENCY, 2);
        self::insertRefs(self::PARAMETER_TYPE, 'Наличие балансировочных вентилей', ParameterType::BALANCE, 2);
        self::insertRefs(self::PARAMETER_TYPE, 'Состояние электрических сетей', ParameterType::POWER_STATUS, 2);
        self::insertRefs(self::PARAMETER_TYPE, 'Состояние трубопроводов и стояков СО', ParameterType::PIPE_STATUS, 2);
        self::insertRefs(self::PARAMETER_TYPE, 'Охват помещений счетчиками тепла и ГВС', ParameterType::HEAT_COUNTER, 2);
        self::insertRefs(self::PARAMETER_TYPE, 'Утепленность трубопроводов', ParameterType::HEAT_PIPE_INSULATION, 2);
        self::insertRefs(self::PARAMETER_TYPE, 'Утепленность оконных блоков', ParameterType::WINDOW_INSULATION, 2);

        /** @var Objects [] $allObjects */
        $allObjects = Objects::find()->where(['objectTypeUuid' => ObjectType::OBJECT])->all();
        foreach ($allObjects as $object) {
            $object->checkCreateParameter(ParameterType::DOOR_EFFICIENCY, 0.2);
            $object->checkCreateParameter(ParameterType::HEAT_REGULATOR, 0.2);
            $object->checkCreateParameter(ParameterType::FLOOR_EFFICIENCY, 0.8);
            $object->checkCreateParameter(ParameterType::AITP, 0);
            $object->checkCreateParameter(ParameterType::PANEL, 1);
            $object->checkCreateParameter(ParameterType::BASEMENT_EFFICIENCY, 1);
            $object->checkCreateParameter(ParameterType::OUTDOOR_LIGHT, 0.2);
            $object->checkCreateParameter(ParameterType::INDOOR_LIGHT, 0.5);
            $object->checkCreateParameter(ParameterType::MOVEMENT_LIGHT, 0.1);
            $object->checkCreateParameter(ParameterType::WINDOWS_EFFICIENCY, 0.2);
            $object->checkCreateParameter(ParameterType::PIPES_RADIATORS, 0.2);
            $object->checkCreateParameter(ParameterType::HEAT_EFFICIENCY, 0.5);
            $object->checkCreateParameter(ParameterType::WALL_EFFICIENCY, 0.6);
            $object->checkCreateParameter(ParameterType::BOILER_EFFICIENCY, 0.2);
            $object->checkCreateParameter(ParameterType::HEAT_GENERATION_EFFICIENCY, 0.2);
            $object->checkCreateParameter(ParameterType::BALANCE, 0);
            $object->checkCreateParameter(ParameterType::POWER_STATUS, 0.75);
            $object->checkCreateParameter(ParameterType::PIPE_STATUS, 0.5);
            $object->checkCreateParameter(ParameterType::HEAT_COUNTER, 0);
            $object->checkCreateParameter(ParameterType::HEAT_PIPE_INSULATION, 0.5);
            $object->checkCreateParameter(ParameterType::WINDOW_INSULATION, 0.8);
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
        echo "m200817_063306_add_fields_for_calculates cannot be reverted.\n";

        return false;
    }

}
