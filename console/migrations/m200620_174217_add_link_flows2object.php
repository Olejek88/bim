<?php

use yii\db\Migration;

/**
 * Class m200622_174217_add_link_flows2object
 */
class m200620_174217_add_link_flows2object extends Migration
{
    const LINK_FLOWS2OBJECT = 'link_flows2object';
    const OBJECT = 'object';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(self::LINK_FLOWS2OBJECT, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(36)->notNull()->unique(),
            'flowId' => $this->integer()->notNull(),
            'objectUuid' => $this->string(36)->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->addForeignKey(
            'fk-' . self::LINK_FLOWS2OBJECT . '-objectUuid',
            self::LINK_FLOWS2OBJECT,
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
        echo "m200622_174217_add_link_flows2object cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200622_174217_add_link_flows2object cannot be reverted.\n";

        return false;
    }
    */
}
