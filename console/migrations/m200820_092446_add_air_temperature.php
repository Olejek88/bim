<?php

use common\components\MainFunctions;
use common\models\MeasureChannel;
use common\models\MeasureType;
use common\models\Objects;
use common\models\ObjectType;
use common\models\Parameter;
use yii\db\Migration;

/**
 * Class m200820_092446_add_air_temperature
 */
class m200820_092446_add_air_temperature extends Migration
{
    const MEASURE_TYPE = '{{%measure_type}}';

    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function safeUp()
    {
        self::insertRefs(self::MEASURE_TYPE, 'Температура наружного воздуха', MeasureType::TEMPERATURE_AIR);
        $this->createNewChannel(MeasureType::MEASURE_TYPE_MONTH);
        $this->createNewChannel(MeasureType::MEASURE_TYPE_DAYS);
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
     * @param $type
     * @return Parameter|null
     * @throws \Exception
     */
    public function createNewChannel($type)
    {
        $object = Objects::find()->where(['objectTypeUuid' => ObjectType::CITY])->andWhere(['deleted' => 0])->limit(1)->one();
        if ($object) {
            $measureChannel = new MeasureChannel();
            $measureChannel->uuid = MainFunctions::GUID();
            $measureChannel->title = 'Температура наружного воздуха';
            $measureChannel->objectUuid = $object['uuid'];
            $measureChannel->measureTypeUuid = MeasureType::TEMPERATURE_AIR;
            $measureChannel->deleted = 0;
            $measureChannel->path = 'external';
            $measureChannel->original_name = '';
            $measureChannel->param_id = "0";
            $measureChannel->type = $type;
            $measureChannel->status = 1;
            $measureChannel->save();
            echo json_encode($measureChannel->errors) . PHP_EOL;
        } else {
            echo 'no city objects found';
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return true;
    }
}
