<?php

use common\models\User;
use yii\db\Migration;

/**
 * Class m200703_091657_add_roles_description
 */
class m200703_091657_add_roles_description extends Migration
{
    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function safeUp()
    {
        $am = Yii::$app->getAuthManager();
        $role = $am->getRole(User::ROLE_ADMIN);
        $role->description = 'Администратор';
        $am->update(User::ROLE_ADMIN, $role);

        $role = $am->getRole(User::ROLE_OPERATOR);
        $role->description = 'Оператор';
        $am->update(User::ROLE_OPERATOR, $role);

        $role = $am->getRole(User::ROLE_USER);
        $role->description = 'Пользователь';
        $am->update(User::ROLE_USER, $role);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200703_091657_add_roles_description cannot be reverted.\n";

        return false;
    }
}
