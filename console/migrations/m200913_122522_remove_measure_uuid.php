<?php

use yii\db\Migration;

/**
 * Class m200913_122522_remove_measure_uuid
 */
class m200913_122522_remove_measure_uuid extends Migration
{
    /**
     * {@inheritdoc}
     * @throws \yii\db\Exception
     */
    public function safeUp()
    {
        $this->addColumn('{{%measure}}', 'measureChannelId', $this->integer()->notNull()->defaultValue(0));
        $time = $this->beginCommand("Set foreign key measureChannelId");
        $this->db->createCommand('update measure
    left join measure_channel on measure.measureChannelUuid=measure_channel.uuid
set measure.measureChannelId=measure_channel._id;
')->execute();
        $this->endCommand($time);
        $this->dropForeignKey('fk-measure-measureChannelUuid', 'measure');
        $this->dropColumn('measure', 'measureChannelUuid');
        $this->dropColumn('measure', 'uuid');
        $this->addForeignKey(
            'fk-measure-measureChannelId',
            '{{%measure}}',
            'measureChannelId',
            '{{%measure_channel}}',
            '_id');
        $this->createIndex('idx_measure_date', '{{%measure}}', 'date');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200913_122522_remove_measure_uuid cannot be reverted.\n";
        $this->dropColumn('{{%measure}}', 'measureChannelId');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200913_122522_remove_measure_uuid cannot be reverted.\n";

        return false;
    }
    */
}
