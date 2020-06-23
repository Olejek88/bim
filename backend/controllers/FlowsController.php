<?php


namespace backend\controllers;

use common\models\Flows;
use Yii;
use yii\base\InvalidConfigException;
use yii\filters\AccessControl;
use yii\web\Controller;

class FlowsController extends Controller
{

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
     * @throws InvalidConfigException
     */
    public function actionIndex()
    {
//        $data = Flows::find()->asArray()->all();
        $data = [
            ['ID' => 1, 'PATH' => 'l1, l2', 'NAME' => 'c1'],
            ['ID' => 2, 'PATH' => 'l1, l2', 'NAME' => 'c2'],
            ['ID' => 3, 'PATH' => 'l1, l2, l3', 'NAME' => 'c3'],
        ];

        // строим дерево из сырых данных
        $tree = [];
        foreach ($data as $channel) {
            $list = explode(', ', $channel['PATH'] . ', ' . $channel['NAME']);
            $array = self::getBranch($list, $channel['ID']);
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
     * @return array
     */
    public function getBranch($list, $id)
    {
        $tree = [];
        $curIdx = array_shift($list);

        if (count($list) != 0) {
            $tree[$curIdx] = self::getBranch($list, $id);
        } else {
            $tree[$curIdx] = ['data' => ['nodeId' => $id]];
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
                    'title' => $name,
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
     * @throws InvalidConfigException
     */
    public function actionBindObject()
    {
        $request = Yii::$app->request;
        if ($request->isPost && $request->isAjax) {
            $folder = $request->getBodyParam('folder');
            $path = $request->getBodyParam('path');
            $id = $request->getBodyParam('id');

            if ($folder) {
                $flows = Flows::findAll(['like', 'PATH', $path]);
            } else {
                $flows[] = Flows::findOne($id);
            }

            foreach ($flows as $flow) {
                $linkFlowsObject = new LinkFlowsObject();
            }
        }
    }
}