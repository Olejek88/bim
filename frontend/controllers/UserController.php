<?php

namespace frontend\controllers;

use common\components\MainFunctions;
use common\models\ActionRegister;
use common\models\User;
use Exception;
use frontend\models\Role;
use frontend\models\UsersSearch;
use Throwable;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends PoliterController
{
    protected $modelClass = User::class;

    /**
     * Lists all User models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        if (isset($_POST['editableAttribute'])) {
            $model = User::find()
                ->where(['id' => $_POST['editableKey']])
                ->one();
            if ($_POST['editableAttribute'] == 'name') {
                $model['name'] = $_POST['User'][$_POST['editableIndex']]['name'];
                $model->save();
                return json_encode($model->errors);
            }
            if ($_POST['editableAttribute'] == 'username') {
                $model['username'] = $_POST['User'][$_POST['editableIndex']]['username'];
                $model->save();
                return json_encode($model->errors);
            }
            if ($_POST['editableAttribute'] == 'type') {
                $model['type'] = intval($_POST['User'][$_POST['editableIndex']]['type']);
                $model->save();
                return json_encode($model->errors);
            }
            if ($_POST['editableAttribute'] == 'email') {
                $model['email'] = $_POST['User'][$_POST['editableIndex']]['email'];
                $model->save();
                return json_encode($model->errors);
            }
            if ($_POST['editableAttribute'] == 'active') {
                $model['active'] = $_POST['User'][$_POST['editableIndex']]['active'];
                $model->save();
                return json_encode($model->errors);
            }
        }
        $searchModel = new UsersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 15;
        return $this->render(
            'index',
            [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]
        );
    }

    /**
     * Displays a single User model.
     *
     * @param integer $id Id.
     *
     * @return mixed
     * @throws \yii\base\Exception
     * @throws Throwable
     */
    public function actionView($id)
    {
        ini_set('memory_limit', '-1');
        $am = Yii::$app->getAuthManager();
        /** @var User $identity */
        $identity = Yii::$app->user->getIdentity();
        if (Yii::$app->user->can(User::ROLE_ADMIN) || $identity->user->_id == $id) {
        } else {
            Yii::$app->session->setFlash('warning', '<h3>'
                . Yii::t('app', 'Не достаточно прав доступа.') . '</h3>');
            $this->redirect('/');
        }

        $user = $this->findModel($id);
        if ($user) {
            $user_property['register'] = ActionRegister::find()->where(['userId' => $user['id']])->count();
            $events = [];
            $registers = ActionRegister::find()
                ->where(['=', 'userId', $user['id']])
                ->all();
            foreach ($registers as $register) {
                $type = '<a class="btn btn-warning btn-xs">' . Yii::t('app', 'Другое действие') . '</a>';
                if ($register['type'] == ActionRegister::TYPE_UPDATE)
                    $type = '<a class="btn btn-warning btn-xs">' . Yii::t('app', 'Обновление') . '</a>';
                if ($register['type'] == ActionRegister::TYPE_DELETE)
                    $type = '<a class="btn btn-error btn-xs">' . Yii::t('app', 'Удаление') . '</a>';
                if ($register['type'] == ActionRegister::TYPE_ADD)
                    $type = '<a class="btn btn-success btn-xs">' . Yii::t('app', 'Добавление') . '</a>';
                if ($register['type'] == ActionRegister::TYPE_EDIT)
                    $type = '<a class="btn btn-warning btn-xs">' . Yii::t('app', 'Редактирование') . '</a>';
                $text = '<a class="btn btn-default btn-xs">' . $register['title'] . '</a><br/>
                <i class="fa fa-cogs"></i>&nbsp;' . Yii::t('app', 'Тип') . ': ' . $type . '<br/>
                <i class="fa fa-check-square"></i>&nbsp;' . Yii::t('app', 'Связано с ') . ': ' . $register->getEntityName() . '';
                $events[] = ['date' => $register['createdAt'], 'event' => self::formEvent($register['createdAt'], 'register', $register['_id'],
                    $register->title, $text)];
            }
            $sort_events = MainFunctions::array_msort($events, ['date' => SORT_DESC]);

            $defaultRole = User::ROLE_OPERATOR;
            $userRoles = $am->getRolesByUser($user->id);
            if (!empty($userRoles)) {
                foreach ($userRoles as $userRole) {
                    $defaultRole = $userRole->name;
                    break;
                }
            }

            $role = new Role();
            // значение по умолчанию
            $role->role = $defaultRole;
            $roles = $am->getRoles();
            $assignments = $am->getAssignments($user->id);
            foreach ($assignments as $value) {
                if (key_exists($value->roleName, $roles)) {
                    $role->role = $value->roleName;
                    break;
                }
            }

            $roleList = ArrayHelper::map($roles, 'name', 'description');
            return $this->render(
                'view',
                [
                    'model' => $user,
                    'user_property' => $user_property,
                    'events' => $sort_events,
                    'role' => $role,
                    'roleList' => $roleList,
                ]
            );
        } else {
            return $this->redirect(['user']);
        }
    }

    /**
     * Finds the User model based on its primary key value.
     * @param integer $id Id.
     *
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Запрашиваемая страница не существует.'));
        }
    }

    /**
     * Формируем код записи о событии
     * @param $date
     * @param $type
     * @param $id
     * @param $title
     * @param $text
     *
     * @return string
     */
    public static function formEvent($date, $type, $id, $title, $text)
    {
        $event = '<li>';
        if ($type == 'register')
            $event .= '<i class="fa fa-wrench bg-red"></i>';

        $event .= '<div class="timeline-item">';
        $event .= '<span class="time"><i class="fa fa-clock-o"></i> ' . date("M j, Y h:i", strtotime($date)) . '</span>';
        if ($type == 'register')
            $event .= '<h3 class="timeline-header">' . Yii::t('app', 'Пользователь &nbsp;') . $title . '</h3>';
        $event .= '<div class="timeline-body" style="min-height: 100px;">' . $text . '</div>';
        $event .= '</div></li>';
        return $event;
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     * @throws \yii\base\Exception
     * @throws Exception
     */
    public function actionNew()
    {
        $model = new User();
        $am = Yii::$app->getAuthManager();
        $roles = $am->getRoles();
        $roleList = ArrayHelper::map($roles, 'name', 'description');
        $role = new Role();
        // значение по умолчанию
        $role->role = User::ROLE_OPERATOR;
        $model->type = 1;
        $model->auth_key = Yii::$app->security->generateRandomString();

        if ($model->load(Yii::$app->request->post())) {
            // получаем изображение для последующего сохранения
            $file = UploadedFile::getInstance($model, 'image');
            if ($file && $file->tempName) {
                $fileName = self::_saveFile($model, $file);
                if ($fileName) {
                    $model->image = $fileName;
                } else {
                    // уведомить пользователя, админа о невозможности сохранить файл
                    Yii::error(Yii::t('app', 'Невозможно сохранить файл изображения пользователя!'));
                }
            }

            $usersParams = Yii::$app->request->getBodyParam('User', null);
            $pass = $usersParams['pass'];
            if (!empty($pass)) {
                $model->password_hash = Yii::$app->security->generatePasswordHash($pass);
            }

            if ($model->save()) {
                MainFunctions::register(Yii::t('app', 'Добавлен пользователь ') . $model->name,
                    ActionRegister::TYPE_ADD,
                    $model->id . "");

                if (!empty($pass)) {
                    // обновляем пароль
                    $model->setPassword($pass);
                    $model->save();
                }

                if ($role->load(Yii::$app->request->post())) {
                    $newRole = $am->getRole($role->role);
                    $am->assign($newRole, $model->id);
                }
                return $this->redirect(['/user']);
            }
        }
        return $this->renderAjax('../user/_edit_users', [
            'model' => $model,
            'role' => $role,
            'roleList' => $roleList,
        ]);
    }

    /**
     * Сохраняем файл согласно нашим правилам.
     *
     * @param User $model Пользователь
     * @param UploadedFile $file Файл
     *
     * @return string | null
     */
    private static function _saveFile($model, $file)
    {
        $dir = $model->getImageDir();
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0755, true)) {
                return null;
            }
        }

        $targetDir = Yii::getAlias($dir);
        $fileName = $model->id . '.' . $file->extension;
        if ($file->saveAs($targetDir . $fileName)) {
            return $fileName;
        } else {
            return null;
        }
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     * @throws \yii\base\Exception
     * @throws Exception
     */
    public function actionSave()
    {
        $model = new User();
        $model->type = 1;
        $am = Yii::$app->getAuthManager();
        $roles = $am->getRoles();
        $roleList = ArrayHelper::map($roles, 'name', 'description');
        $role = new Role();
        // значение по умолчанию
        $role->role = User::ROLE_OPERATOR;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            // получаем изображение для последующего сохранения
            $file = UploadedFile::getInstance($model, 'image');
            if ($file && $file->tempName) {
                $fileName = self::_saveFile($model, $file);
                if ($fileName) {
                    $model->image = $fileName;
                } else {
                    // уведомить пользователя, админа о невозможности сохранить файл
                    Yii::error(Yii::t('app', 'Невозможно сохранить файл изображения пользователя!'));
                }
            }

            $usersParams = Yii::$app->request->getBodyParam('User', null);
            $pass = $usersParams['pass'];
            if (!empty($pass)) {
                $model->password_hash = Yii::$app->security->generatePasswordHash($pass);
            }

            if ($model->save()) {
                MainFunctions::register(Yii::t('app', 'Добавлен пользователь ') . $model->name,
                    ActionRegister::TYPE_ADD,
                    $model->id . "");

                if (!empty($pass)) {
                    // обновляем пароль
                    $model->setPassword($pass);
                    $model->save();
                }

                if ($role->load(Yii::$app->request->post())) {
                    $newRole = $am->getRole($role->role);
                    $am->assign($newRole, $model->id);
                }
                return $this->redirect(['/user']);
            }
        }
        return $this->renderAjax('../user/_edit_users', [
            'model' => $model,
            'role' => $role,
            'roleList' => $roleList,
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id Id.
     *
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \yii\base\Exception
     * @throws Exception
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        // сохраняем старое значение image
        $oldImage = $model->image;
        $am = Yii::$app->getAuthManager();
        $defaultRole = User::ROLE_OPERATOR;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            // получаем изображение для последующего сохранения
            $file = UploadedFile::getInstance($model, 'image');
            if ($file && $file->tempName) {
                $fileName = self::_saveFile($model, $file);
                if ($fileName) {
                    $model->image = $fileName;
                } else {
                    $model->image = $oldImage;
                    // уведомить пользователя, админа о невозможности сохранить файл
                }
            } else {
                $model->image = $oldImage;
            }

            $usersParams = Yii::$app->request->getBodyParam('User', null);
            $pass = $usersParams['pass'];
            if (!empty($pass)) {
                $model->password_hash = Yii::$app->security->generatePasswordHash($pass);
            }

            if ($model->save()) {
                MainFunctions::register(Yii::t('app', 'Обновлен профиль пользователя ') . $model->name, ActionRegister::TYPE_EDIT, $id . "");
                // обновляем разрешения пользователя
                $newRoleModel = new Role();
                if ($newRoleModel->load(Yii::$app->request->post())) {
                    $newRole = $am->getRole($newRoleModel->role);
                    try {
                        // удаляем все назначения прав связанных с ролями
                        $userId = $model->id;
                        $userRoles = $am->getRolesByUser($userId);
                        foreach ($userRoles as $userRole) {
                            $am->revoke($userRole, $userId);
                        }

                        $am->assign($newRole, $userId);
                    } catch (Exception $e) {
                        // видимо такое разрешение есть
                    }
                }
                return $this->redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $userRoles = $am->getRolesByUser($model->userId);
            if (!empty($userRoles)) {
                foreach ($userRoles as $userRole) {
                    $defaultRole = $userRole->name;
                    break;
                }
            }
        }

        $role = new Role();
        // значение по умолчанию
        $role->role = $defaultRole;
        $roles = $am->getRoles();
        $assignments = $am->getAssignments($id);
        foreach ($assignments as $value) {
            if (key_exists($value->roleName, $roles)) {
                $role->role = $value->roleName;
                break;
            }
        }

        $roleList = ArrayHelper::map($roles, 'name', 'description');
        return $this->render(
            'update',
            [
                'model' => $model,
                'role' => $role,
                'roleList' => $roleList
            ]
        );
    }

    /**
     * Deletes an existing User model.
     *
     * @param integer $id Id.
     *
     * @return mixed
     * @throws Exception
     * @throws Throwable
     */
    public function actionDelete($id)
    {
        /** @var User $user */
        $user = User::findOne($id);
        if ($user != null) {
            $user->active = 0;
            $user->save();
            return $this->redirect(['/user/view', 'id' => $user->id]);
        } else {
            return $this->redirect(['/user/index']);
        }
    }

    /**
     * @param $action
     * @return bool
     * @throws BadRequestHttpException
     */
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @return int|string
     * @throws NotFoundHttpException
     */
    public function actionEdit()
    {
        if (isset ($_GET["id"]))
            $id = $_GET["id"];
        else
            return null;

        $model = $this->findModel($id);

        $am = Yii::$app->getAuthManager();
        $defaultRole = User::ROLE_OPERATOR;
        $userRoles = $am->getRolesByUser($model->id);
        if (!empty($userRoles)) {
            foreach ($userRoles as $userRole) {
                $defaultRole = $userRole->name;
                break;
            }
        }

        $role = new Role();
        // значение по умолчанию
        $role->role = $defaultRole;
        $roles = $am->getRoles();
        $assignments = $am->getAssignments($id);
        foreach ($assignments as $value) {
            if (key_exists($value->roleName, $roles)) {
                $role->role = $value->roleName;
                break;
            }
        }
        $roleList = ArrayHelper::map($roles, 'name', 'description');
        return $this->renderAjax('../user/_edit_users', [
            'model' => $model,
            'role' => $role,
            'roleList' => $roleList,
        ]);
    }

    /**
     * Displays a single User timeline.
     *
     * @param integer $id Id.
     *
     * @return mixed
     * @throws Throwable
     */
    public function actionTimeline($id)
    {
        ini_set('memory_limit', '-1');

        if (!empty($_GET['type']) && is_numeric($_GET['type'])) {
            $type = intval($_GET['type']);
        } else {
            $type = null;
        }

        try {
            $user = $this->findModel($id);
        } catch (Exception $exception) {
            return $this->redirect(['/user']);
        }

        $events = [];

        $registers = ActionRegister::find()
            ->where(['=', 'userId', $user['id']])
            ->orderBy('createdAt DESC')
            ->limit(50)
            ->all();
        foreach ($registers as $register) {
            $status = '<a class="btn btn-success btn-xs">' . Yii::t('app', 'Информация') . '</a>';
            if ($register['type'] == 1)
                $status = '<a class="btn btn-warning btn-xs">' . Yii::t('app', 'Предупреждение') . '</a>';
            if ($register['type'] == 2)
                $status = '<a class="btn btn-error btn-xs">' . Yii::t('app', 'Ошибка') . '</a>';

            $text = '<a class="btn btn-default btn-xs">' . $register->title . '</a><br/>
                <i class="fa fa-cogs"></i>&nbsp;' . $register->getEntityName() . '<br/>
                <i class="fa fa-check-square"></i>&nbsp;' . $status . '';
            $events[] = ['date' => $register['createdAt'], 'event' => self::formEvent($register['createdAt'], 'register', $register['_id'],
                $register['title'], $text)];
        }
        $sort_events = MainFunctions::array_msort($events, ['date' => SORT_DESC]);
        $today = date("j-m-Y h:i");
        return $this->render(
            'timeline',
            [
                'events' => $sort_events,
                'today_date' => $today,
                'type' => $type,
                'id' => $id,
            ]
        );
    }
}
