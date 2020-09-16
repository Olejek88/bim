<?php

use yii\db\Expression;
use yii\db\Migration;

/**
 * Class m200916_103630_fix_param_id
 */
class m200916_103630_fix_param_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->update('{{%measure_channel}}', ['param_id' => new Expression('concat(path, _id)')],
            ['path' => ['external', 'internal']]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200916_103630_fix_param_id cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200916_103630_fix_param_id cannot be reverted.\n";

        return false;
    }
    */
}
