<?php

namespace console\controllers;

use common\models\PoliterModel;
use common\models\User;
use Exception;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;
use yii\helpers\FileHelper;

/**
 * Class PoliterAccessController
 * @package console\controllers
 */
class PoliterAccessController extends Controller
{
    public $defaultAction = 'create';

    /**
     * @var string  the DB connection object or the application component ID of the DB connection to use.
     */
    public $db = 'db';

    /**
     * @param $actionID
     * @return array|string[]
     */
    public function options($actionID)
    {
        $options = parent::options($actionID);
        $options[] = 'db';
        return $options;
    }


    /**
     * @return int
     * @throws Exception
     */
    public function actionCreate()
    {
//        $this->stdout("Hello?\n $dbName \n", Console::BOLD|Console::FG_RED);
        $newDb = $this->db;
        try {
            Yii::$app->set('db', Yii::$app->$newDb);
        } catch (Exception $exception) {
            $msg = $this->ansiFormat($exception, Console::BG_RED);
            echo $msg . PHP_EOL;
            return ExitCode::UNSPECIFIED_ERROR;
        }

        // подгружаем классы моделей, строим список всех разрешений доступных из моделей
        $classes = FileHelper::findFiles('common/models/');
        $permsByModel = [];
        foreach ($classes as $class) {
            if ($class !== 'common/models/PoliterTrait.php') {
                $class = str_replace('/', '\\', $class);
                $class = str_replace('.php', '', $class);
                if ($class != 'common\models\PoliterModel') {
                    try {
                        if (!in_array($class, get_declared_classes())) {
                            Yii::autoload($class);
                        }
                    } catch (Exception $exception) {
                        $msg = $this->ansiFormat($exception, Console::BG_RED);
                        echo $msg . PHP_EOL;
                        return ExitCode::UNSPECIFIED_ERROR;
                    }

                    /** @var PoliterModel $model */
                    if (in_array('common\models\PoliterModel', class_parents($class))) {
                        $model = new $class;
                        $perms = $model->getPermissions();
                        $modelName = explode('\\', $class);
                        $modelName = $modelName[count($modelName) - 1];
                        $permsByModel[$modelName] = $perms;
                    }
                }
            }
        }

        // разрешения предоставляемые моделями которые не наследуют common\models\PoliterModel
        $excludeModels = [
            'common\models\Flow',
            'common\models\FlowArchive',
            'common\models\Flows',
            'common\models\Flows2',
        ];

        foreach ($excludeModels as $class) {
            $class = str_replace('/', '\\', $class);
            $class = str_replace('.php', '', $class);
            try {
                if (!in_array($class, get_declared_classes())) {
                    Yii::autoload($class);
                }
            } catch (Exception $exception) {
                $msg = $this->ansiFormat($exception, Console::BG_RED);
                echo $msg . PHP_EOL;
                return ExitCode::UNSPECIFIED_ERROR;
            }

            $model = new $class;
            $perms = $model->getPermissions();
            $modelName = explode('\\', $class);
            $modelName = $modelName[count($modelName) - 1];
            $permsByModel[$modelName] = $perms;
        }

        // добавляем отсутствующие разрешения
        $am = Yii::$app->getAuthManager();

        $role = $am->getRole(User::ROLE_ADMIN);
        if ($role == null) {
            $role = $am->createRole(User::ROLE_ADMIN);
            $am->add($role);
        }

        $role = $am->getRole(User::ROLE_OPERATOR);
        if ($role == null) {
            $role = $am->createRole(User::ROLE_OPERATOR);
            $am->add($role);
        }

        $role = $am->getRole(User::ROLE_USER);
        if ($role == null) {
            $role = $am->createRole(User::ROLE_USER);
            $am->add($role);
        }

        $rbacPermissions = $am->getPermissions();
        foreach ($permsByModel as $modelName => $perms) {
            foreach ($perms as $perm) {
                //Проверка на массив если были добавлены описания
                $perm_description = null;
                if (is_array($perm)) { //Проверяем на массив с описанием
                    $perm_description = $perm['description'];
                    $perm = $perm['name'];
                }

                if (!isset($rbacPermissions[$perm])) {
                    try {
                        $permission = $am->createPermission($perm);
                        if ($perm_description) {
                            $permission->description = $perm_description;
                        }

                        $am->add($permission);
                        $role = $am->getRole(User::ROLE_ADMIN);
                        if (!is_null($role)) {
                            $am->addChild($role, $permission);
                        }

                        echo 'add ' . $perm . ', description : ' . $perm_description . PHP_EOL;
                    } catch (Exception $exception) {
                        $msg = $this->ansiFormat($exception, Console::BG_RED);
                        echo $msg . PHP_EOL;
                        return ExitCode::UNSPECIFIED_ERROR;
                    }
                } else {
                    // если есть описание к разрешению, обновляем запись в базе
                    if ($perm_description) {
                        $permission = $rbacPermissions[$perm];
                        $permission->description = $perm_description;
                        try {
                            $am->update($permission->name, $permission);
                        } catch (Exception $exception) {
                            $msg = $this->ansiFormat($exception, Console::BG_RED);
                            echo $msg . PHP_EOL;
                            return ExitCode::UNSPECIFIED_ERROR;
                        }
                    }

                    unset($rbacPermissions[$perm]);
                }
            }
        }

        // удаляем все разрешения которые не связанны с моделями, кроме тех которые заданы в списке
        $constPermissions = [
            User::PERMISSION_ADMIN,
            User::PERMISSION_OPERATOR,
            User::PERMISSION_USER,
        ];
        foreach ($rbacPermissions as $rbacPerm) {
            if (!in_array($rbacPerm->name, $constPermissions)) {
                echo 'delete ' . $rbacPerm->name . PHP_EOL;
                $am->remove($rbacPerm);
            }
        }

        return ExitCode::OK;
    }
}
