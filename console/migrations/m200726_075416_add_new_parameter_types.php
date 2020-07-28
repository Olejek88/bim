<?php

use common\models\Objects;
use common\models\ObjectType;
use common\models\ParameterType;
use frontend\controllers\ObjectController;
use yii\db\Migration;

/**
 * Class m200726_075416_add_new_parameter_types
 */
class m200726_075416_add_new_parameter_types extends Migration
{
    const PARAMETER_TYPE = '{{%parameter_type}}';

    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function safeUp()
    {
        $this->addColumn('event', 'dateFact', $this->timestamp());
        self::insertRefs(self::PARAMETER_TYPE, 'Базовый уровень потребления', ParameterType::BASE_CONSUMPTION);
        self::insertRefs(self::PARAMETER_TYPE, 'Коэффициент потребления месяца', ParameterType::CONSUMPTION_COEFFICIENT);
        $allObjects = Objects::find()->where(['objectTypeUuid' => ObjectType::OBJECT])->all();
        foreach ($allObjects as $object) {
            ObjectController::createConsumptionCoefficients($object['uuid']);
        }
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
        echo "m200726_075416_add_new_parameter_types cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200726_075416_add_new_parameter_types cannot be reverted.\n";

        return false;
    }
    */
}
