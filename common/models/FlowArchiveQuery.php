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
        if (/*isset($condition['ID']) && */ !empty($condition['ID'])) {
            $params[':id'] = $condition['ID'];
        } else {
            throw new InvalidConfigException();
        }

        if (/*isset($condition['ID']) && */ !empty($condition['fromTime'])) {
            $params[':fromTime'] = $condition['fromTime'];
        } else {
            $params[':fromTime'] = null;
        }

        if (/*isset($condition['ID']) && */ !empty($condition['toTime'])) {
            $params[':toTime'] = $condition['toTime'];
        } else {
            $params[':toTime'] = null;
        }


        $this->addParams($params);
        return $this;
    }

}