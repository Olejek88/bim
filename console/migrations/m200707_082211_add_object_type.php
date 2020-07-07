<?php

use common\models\ObjectType;
use yii\db\Migration;

/**
 * Class m200707_082211_add_object_type
 */
class m200707_082211_add_object_type extends Migration
{
    const OBJECT_TYPE = '{{%object_type}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        self::insertRefs(self::OBJECT_TYPE, 'Объект', ObjectType::OBJECT);
    }

    private function insertRefs($table, $title, $uuid)
    {
        $date = date('Y-m-d\TH:i:s');
        $this->insert($table, [
            'uuid' => $uuid,
            'title' => $title,
            'createdAt' => $date,
            'changedAt' => $date,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200707_082211_add_object_type cannot be reverted.\n";

        return false;
    }
}
