<?php


namespace common\models;


use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ExpressionInterface;

/**
 * Class FlowArchiveQuery
 * @package common\models
 * @see TestModel
 */
class FlowArchiveQuery extends ActiveQuery
{
    /**
     * @param array|string|ExpressionInterface $condition
     * @param array $params
     * @return $this|ActiveQuery
     * @throws InvalidConfigException
     */
    public function where($condition, $params = [])
    {
        $params = [];
        if (!empty($condition['ID'])) {
            $params[':id'] = $condition['ID'];
        } else {
            throw new InvalidConfigException();
        }

        if (!empty($condition['fromTime'])) {
            $params[':fromTime'] = date('Y-m-d H:i:s', strtotime($condition['fromTime']));
            unset($condition['fromTime']);
        } else {
            $params[':fromTime'] = null;
        }

        if (!empty($condition['toTime'])) {
            $params[':toTime'] = date('Y-m-d H:i:s', strtotime($condition['toTime']));;
            unset($condition['toTime']);
        } else {
            $params[':toTime'] = null;
        }


        $this->addParams($params);
        return $this;
    }

}