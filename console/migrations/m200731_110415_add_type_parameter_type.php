<?php

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
     */
    public function safeUp()
    {
        $this->addColumn(self::PARAMETER_TYPE, 'type', $this->integer()->defaultValue(0));

        self::insertRefs(self::PARAMETER_TYPE, 'Базовый уровень потребления', ParameterType::BASE_CONSUMPTION, 1);
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
