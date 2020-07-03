<?php

namespace frontend\models;

use common\models\User;
use yii\base\Model;

class Role extends Model
{
    public $role;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['role'], 'string', 'max' => 128],
            ['role', 'in', 'range' => [
                User::ROLE_ADMIN,
                User::ROLE_OPERATOR,
                User::ROLE_USER,
            ],
                'strict' => true],
        ];
    }
}