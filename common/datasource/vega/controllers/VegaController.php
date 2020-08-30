<?php


namespace common\datasource\vega\controllers;

use frontend\controllers\PoliterController;
use SPF\Client\WebSocket;
use SPF\Http\WebSocketException;
use stdClass;
use Yii;
use yii\filters\AccessControl;

/**
 *
 * @property-read array|string[][] $permissions
 */
class VegaController extends PoliterController
{
    protected $modelClass = \common\datasource\vega\models\VegaController::class;

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }


    /**
     * @return string
     * @throws WebSocketException
     */
    public function actionIndex()
    {
        // для того чтоб правильно формировались url, редиректим на полный путь модуля
        if (Yii::$app->controller->id == 'default') {
            return Yii::$app->response->redirect('/' . $this->module->id . '/vega/index');
        }

        // TODO: запрос всех устройств/данных с сервера
        // TODO: вход и получение токена вынести в отдельный метод(класс). сокет дергать из него? Сокет в классе, он его и использует
        $server = $this->module->server;
        $ws = new WebSocket($server['host'], $server['port']);
        $message = null;
        $isConnected = $ws->connect();
        $login = false;
        if ($isConnected) {
            $login = $this->login($ws, $server['login'], $server['password']);
            if ($login === false) {
                $message = 'Ошибка входа';
            } else if ($login->status = false) {
                $message = 'Не верный логин или пароль';
            }
        } else {
            $message = 'Нет соединения с сервером - ' . $server['host'] . ':' . $server['port'];
        }

        if ($message != null) {
            return $this->render('index', [
                'data' => [],
                'message' => $message,
            ]);
        }

        $ws = new WebSocket($server['host'], $server['port']);
        $isConnected = $ws->connect();
        if ($isConnected) {
            $token = $this->getToken($ws, $login->token);
            if ($token === false) {
                $message = 'Ошибка получения токена';
            } else if ($token->status === false) {
                $message = 'Токен не получен';
            } else {
                $message = 'Токен получен';
            }
        } else {
            $message = 'Нет соединения с сервером - ' . $server['host'] . ':' . $server['port'];
        }


        $data = [];
        return $this->render('index', [
            'data' => $data,
            'message' => $message,
        ]);
    }

    public function actionTable()
    {
        return $this->render('table', [
        ]);
    }

    /**
     * @param WebSocket $ws
     * @param $login
     * @param $password
     *
     * @return bool|stdClass
     */
    private function login($ws, $login, $password)
    {
        $req = [
            'cmd' => 'auth_req',
            'login' => $login,
            'password' => $password,
        ];

        if (!$ws->sendJson($req)) {
            return false;
        }

        try {
            $answer = $ws->recv();
            if ($answer === false) {
                return false;
            }
        } catch (WebSocketException $exception) {
            return false;
        }

        $ws->disconnect();
        return json_decode($answer);
    }

    /**
     * @param WebSocket $ws
     * @param string $token
     *
     * @return bool|stdClass
     */
    private function getToken($ws, $token)
    {
        $req = [
            'cmd' => 'token_auth_req',
            'token' => $token,
        ];

        if (!$ws->sendJson($req)) {
            return false;
        }

        try {
            $answer = $ws->recv();
            if ($answer === false) {
                return false;
            }
        } catch (WebSocketException $exception) {
            return false;
        }

        return json_decode($answer);
    }

}