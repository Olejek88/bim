<?php

use yii\db\Migration;

/**
 * Class m200623_071601_alter_measure_channel
 */
class m200623_071601_alter_measure_channel extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('measure_channel', 'path', $this->text());
        $this->addColumn('measure_channel', 'original_name', $this->string());
        $this->addColumn('measure_channel', 'param_id', $this->string());
        $this->addColumn('measure_channel', 'type', $this->integer()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200623_071601_alter_measure_channel cannot be reverted.\n";
        $this->dropColumn('measure_channel', 'path');
        $this->dropColumn('measure_channel', 'original_name');
        $this->dropColumn('measure_channel', 'param_id');
        $this->dropColumn('measure_channel', 'type');
        return false;
    }
}
