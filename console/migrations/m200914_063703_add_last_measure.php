<?php

use yii\db\Migration;

/**
 * Class m200914_063703_add_last_measure
 */
class m200914_063703_add_last_measure extends Migration
{
    const MEASURE_LAST = '{{%measure_last}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = '';
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(self::MEASURE_LAST, [
            '_id' => $this->primaryKey(),
            'measureChannelId' => $this->integer()->notNull()->defaultValue(0),
            'value' => $this->double()->defaultValue(0),
            'date' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->addForeignKey(
            'fk-measure-last-measureChannelId',
            self::MEASURE_LAST,
            'measureChannelId',
            '{{%measure_channel}}',
            '_id');
        $this->createIndex('idx_measure_date', self::MEASURE_LAST, 'date');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200914_063703_add_last_measure cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200914_063703_add_last_measure cannot be reverted.\n";

        return false;
    }
    */
}
