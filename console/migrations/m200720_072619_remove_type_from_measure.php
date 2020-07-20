<?php

use yii\db\Migration;

/**
 * Class m200720_072619_remove_type_from_measure
 */
class m200720_072619_remove_type_from_measure extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('{{%measure}}', 'type');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200720_072619_remove_type_from_measure cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200720_072619_remove_type_from_measure cannot be reverted.\n";

        return false;
    }
    */
}
