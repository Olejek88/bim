<?php

use yii\db\Migration;

/**
 * Class m200724_082704_add_status_field
 */
class m200724_082704_add_status_field extends Migration
{
    const MEASURE_CHANNEL = '{{%measure_channel}}';
    const ALARM = '{{%alarm}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(self::MEASURE_CHANNEL, 'status', $this->integer()->defaultValue(1));
        $this->addColumn(self::ALARM, 'status', $this->integer()->defaultValue(1));
        $this->addColumn(self::ALARM, 'type', $this->integer()->defaultValue(0));
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn(self::MEASURE_CHANNEL, 'status');
        $this->dropColumn(self::ALARM, 'status');
        $this->dropColumn(self::ALARM, 'type');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200724_082704_add_status_field cannot be reverted.\n";

        return false;
    }
    */
}
