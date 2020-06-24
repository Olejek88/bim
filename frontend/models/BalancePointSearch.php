<?php

namespace frontend\models;

use common\models\BalancePoint;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * BalancePointSearch represents the model behind the search form about `common\models\BalancePoint`.
 */
class BalancePointSearch extends BalancePoint
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['measureChannelUuid', 'objectUuid', 'input'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = BalancePoint::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            '_id' => $this->_id,
            'input' => $this->input,
            'objectUuid' => $this->objectUuid,
            'measureChannelUuid' => $this->measureChannelUuid,
            'createdAt' => $this->createdAt,
            'changedAt' => $this->changedAt,
        ]);

        $query->orderBy(['changedAt' => SORT_DESC]);

        return $dataProvider;
    }
}
