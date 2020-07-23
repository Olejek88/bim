<?php

use yii\db\Migration;

/**
 * Class m200723_140305_add_new_object_fields
 */
class m200723_140305_add_new_object_fields extends Migration
{
    const OBJECT = '{{%object}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(self::OBJECT, 'source', $this->integer()->defaultValue(0));
        $this->addColumn(self::OBJECT, 'water', $this->integer()->defaultValue(0));
        $this->addColumn(self::OBJECT, 'electricity', $this->integer()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200723_140305_add_new_object_fields cannot be reverted.\n";

        return false;
    }
}
