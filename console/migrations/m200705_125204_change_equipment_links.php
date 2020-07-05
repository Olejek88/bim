<?php

use yii\db\Migration;

/**
 * Class m200705_125204_change_equipment_links
 */
class m200705_125204_change_equipment_links extends Migration
{
    const MEASURE_CHANNEL = '{{%measure_channel}}';
    const OBJECT = '{{%object}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey(
            'fk-measure_channel-equipmentUuid',
            self::MEASURE_CHANNEL);

        $this->dropIndex(
            'idx-equipmentUuid',
            self::MEASURE_CHANNEL);

        $this->renameColumn(self::MEASURE_CHANNEL, 'equipmentUuid', 'objectUuid');

        $this->createIndex(
            'idx-objectUuid',
            self::MEASURE_CHANNEL,
            'objectUuid'
        );

        $this->addForeignKey(
            'fk-measure_channel-objectUuid',
            self::MEASURE_CHANNEL,
            'objectUuid',
            self::OBJECT,
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200705_125204_change_equipment_links cannot be reverted.\n";

        return false;
    }
}
