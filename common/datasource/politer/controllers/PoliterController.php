<?php


namespace common\datasource\politer\controllers;

use Codeception\Util\HttpCode;
use common\components\MainFunctions;
use common\datasource\politer\models\Flows;
use common\models\MeasureChannel;
use common\models\MeasureType;
use common\models\Objects;
use common\models\ObjectType;
use Exception;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

/**
 *
 * @property-read array|string[][] $permissions
 */
class PoliterController extends \frontend\controllers\PoliterController
{
    protected $modelClass = \common\datasource\politer\models\PoliterController::class;

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
     */
    public function actionIndex()
    {
        // для того чтоб правильно формировались url, редиректим на полный путь модуля
        if (Yii::$app->controller->id == 'default') {
            return Yii::$app->response->redirect('/' . $this->module->id . '/politer/index');
        }

        $data = Flows::find()->asArray()->all();

        $localMeasureChannels = MeasureChannel::find()
            ->with('object')
            ->where(['deleted' => false, 'data_source' => $this->module->id])
            ->asArray()
            ->all();
        $localMeasureChannels = ArrayHelper::map($localMeasureChannels, 'param_id', function ($model) {
            return $model['object']['title'];
        });
        // строим дерево из сырых данных
        $tree = [];
        foreach ($data as $channel) {
            $list = explode(', ', $channel['PATH'] . ', ' . $channel['NAME']);
            $array = self::getBranch($list, $channel['ID'], $localMeasureChannels);
            $tree = array_merge_recursive($tree, $array);
        }

        // строим дерево для FancytreeWidget
        $tree = self::getTree($tree);

        return $this->render('tree', [
            'tree' => $tree,
        ]);
    }

    /**
     * @param $list array
     * @param $id integer
     * @param array $localMeasureChannels
     * @return MeasureChannel[]
     */
    public function getBranch($list, $id, &$localMeasureChannels = [])
    {
        $tree = [];
        $curIdx = array_shift($list);

        if (count($list) != 0) {
            $tree[$curIdx] = self::getBranch($list, $id, $localMeasureChannels);
        } else {
            $localObjectTitle = !empty($localMeasureChannels[$id]) ? $localMeasureChannels[$id] : '';
            $tree[$curIdx] = ['data' => ['nodeId' => $id, 'localObjectTitle' => $localObjectTitle]];
        }

        return $tree;
    }

    /**
     * @param $tree array
     * @param $path string
     * @return array
     */
    public function getTree($tree, $path = null)
    {
        $resultTree = [];
        foreach ($tree as $name => $value) {
            if (is_array($value) && count($value) == 1 && isset($value['data'])) {
                $resultTree[] = [
                    'title' => $name . ($value['data']['localObjectTitle'] != '' ? ' (' . $value['data']['localObjectTitle'] . ')' : ''),
                    'data' => $value['data'],
                    'path' => $path,
                ];
            } else if ($name == 'data') {
                continue;
            } else {
                $newPath = $path != null ? $path . ', ' . $name : $name;
                $resultTree[] = ['title' => $name, 'folder' => true, 'path' => $newPath, 'children' => self::getTree($value, $newPath)];
            }
        }

        return $resultTree;
    }

    /**
     * @return array|string
     * @throws Exception
     */
    public function actionLinkObjForm()
    {
        $request = Yii::$app->request;
        if ($request->isPost) {
            $id = null;
            $errors = null;

            $objectsUuid = $request->getBodyParam('objects');
            if (empty($objectsUuid)) {
                $errors[] = 'Выбран не верный объект';
            }

            $folder = $request->getBodyParam('folder');
            if ($folder === 'undefined') {
                $folder = false;
                $id = $request->getBodyParam('id');
                if (intval($id) == 0) {
                    $errors[] = 'Выбран не верный канал измерения';
                }
            } else {
                $folder = true;
            }

            $path = $request->getBodyParam('path');
            if (empty($path)) {
                $errors[] = 'Не верный путь канала измерения';
            }

            $measureTypeUuid = $request->getBodyParam('measureTypeUuid');
            if (empty($measureTypeUuid)) {
                $errors[] = 'Не верный тип измерения';
            }

            $type = $request->getBodyParam('type');
            if ($type == '' || !in_array(intval($type), [0, 1, 2, 4, 7])) {
                $errors[] = 'Не верный тип';
            }

            if (!empty($errors)) {
                $errors = implode(', ', $errors);
                Yii::$app->response->statusCode = HttpCode::NOT_ACCEPTABLE;
                return $errors;
            }

            if ($folder) {
                // выбираем все каналы измерений которые соответствуют пути и связываем их с объектом
                $flows = Flows::find()->where(['like', 'PATH', $path . '%', false])->all();
            } else {
                $flows = [];
                $flows1 = Flows::findOne(['ID' => $id]);
                if ($flows1) {
                    $flows[] = $flows1;
                }
            }

            foreach ($flows as $flow) {
                $measureChannel = new MeasureChannel();
                $measureChannel->uuid = MainFunctions::GUID();
                $measureChannel->title = $flow->NAME;
                $measureChannel->objectUuid = $objectsUuid;
                $measureChannel->measureTypeUuid = $measureTypeUuid;
                $measureChannel->deleted = 0;
                $measureChannel->path = $flow->PATH;
                $measureChannel->original_name = $flow->NAME;
                $measureChannel->param_id = '' . $flow->ID;
                $measureChannel->type = $type; // в документации типы есть, но реально они не возвращаются, пользователь выбирает руками
                $measureChannel->data_source = $this->module->id;
                if (!$measureChannel->save()) {
                    foreach ($measureChannel->errors as $key => $error) {
                        $errors .= $error[0] . ', ';
                    }
                }
            }

            if (!empty($errors)) {
                Yii::$app->response->statusCode = HttpCode::NOT_ACCEPTABLE;
                return $errors;
            } else {
                return '';
            }
        } else if ($request->isAjax) {
            $objects = Objects::findAll(['objectTypeUuid' => ObjectType::OBJECT]);
            $objects = ArrayHelper::map($objects, 'uuid', 'title');
            $types = MeasureType::find()->orderBy('title DESC')->all();
            $types = ArrayHelper::map($types, 'uuid', 'title');
            return $this->renderAjax('_link_obj_form', [
                'objects' => $objects,
                'types' => $types,
            ]);
        } else {
            return '';
        }
    }
}