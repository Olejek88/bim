<?php

use common\models\User;
use yii\db\Migration;

/**
 * Class m200702_141718_add_interface_permissions
 */
class m200702_141718_add_interface_permissions extends Migration
{
    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function up()
    {
        $auth = \Yii::$app->authManager;

        $perm = $auth->createPermission(common\models\User::PERMISSION_ADMIN);
        $auth->add($perm);
        $role = $auth->getRole(User::ROLE_ADMIN);
        $auth->addChild($role, $perm);

        $perm = $auth->createPermission(common\models\User::PERMISSION_OPERATOR);
        $auth->add($perm);
        $role = $auth->getRole(User::ROLE_OPERATOR);
        $auth->addChild($role, $perm);

        $perm = $auth->createPermission(common\models\User::PERMISSION_USER);
        $auth->add($perm);
        $role = $auth->getRole(User::ROLE_USER);
        $auth->addChild($role, $perm);


    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        echo "m190227_100533_add_interface_permission cannot be reverted.\n";

        $auth = \Yii::$app->authManager;

        $role = $auth->getRole(User::ROLE_ADMIN);
        $perm = $auth->getPermission(common\models\User::PERMISSION_ADMIN);
        $auth->removeChild($role, $perm);
        $auth->remove($perm);

        $role = $auth->getRole(User::ROLE_OPERATOR);
        $perm = $auth->getPermission(common\models\User::PERMISSION_OPERATOR);
        $auth->removeChild($role, $perm);
        $auth->remove($perm);

        $role = $auth->getRole(User::ROLE_USER);
        $perm = $auth->getPermission(common\models\User::PERMISSION_USER);
        $auth->removeChild($role, $perm);
        $auth->remove($perm);

        return true;
    }
}
