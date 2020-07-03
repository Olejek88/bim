<?php

use yii\db\Migration;

/**
 * Class m200702_141315_rbac_add_bae_roles
 */
class m200702_141315_rbac_add_base_roles extends Migration
{
    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function up()
    {
        $auth = Yii::$app->authManager;

        $role = $auth->getRole(common\models\User::ROLE_ADMIN);
        if ($role == null) {
            $role = $auth->createRole(common\models\User::ROLE_ADMIN);
            $auth->add($role);
        }

        $role = $auth->getRole(common\models\User::ROLE_OPERATOR);
        if ($role == null) {
            $role = $auth->createRole(common\models\User::ROLE_OPERATOR);
            $auth->add($role);
        }

        $role = $auth->getRole(common\models\User::ROLE_USER);
        if ($role == null) {
            $role = $auth->createRole(common\models\User::ROLE_USER);
            $auth->add($role);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        echo "m190207_072753_rbac_add_base_roles cannot be reverted.\n";

        $auth = Yii::$app->authManager;

        $role = $auth->getRole(common\models\User::ROLE_USER);
        $auth->remove($role);

        $role = $auth->getRole(common\models\User::ROLE_OPERATOR);
        $auth->remove($role);

        $role = $auth->getRole(common\models\User::ROLE_ADMIN);
        $auth->remove($role);

        return true;
    }
}
