<?php

use yii\db\Migration;

/**
 * Class m200803_144846_extend_measure_channel
 */
class m200803_144846_extend_measure_channel extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%measure_channel}}', 'data_source', $this->char(128)->null());
        $this->alterColumn('{{%measure_channel}}', 'param_id', $this->char(255)->null());
        $this->createIndex('idx-measure_channel-param_id-data_source', '{{%measure_channel}}', ['param_id', 'data_source'], true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200803_144846_extend_measure_channel can be reverted.\n";
        $this->dropIndex('idx-measure_channel-param_id-data_source', '{{%measure_channel}}');
        $this->dropColumn('{{%measure_channel}}', 'data_source');
        $this->alterColumn('{{%measure_channel}}', 'param_id', $this->string()->null());
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200803_144846_extend_measure_channel cannot be reverted.\n";

        return false;
    }
    */
}
