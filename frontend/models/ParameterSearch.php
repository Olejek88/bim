<?php

namespace frontend\models;

use common\models\Parameter;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ParameterSearch represents the model behind the search form about `common\models\Parameter`.
 */
class ParameterSearch extends Parameter
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'entityUuid', 'parameterTypeUuid', 'date', 'createdAt', 'changedAt'], 'safe'],
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
        $query = Parameter::find();
        $query->joinWith('parameterType');
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            '_id' => $this->_id,
            'date' => $this->date,
            'entityUuid' => $this->entityUuid,
            'parameterTypeUuid' => $this->parameterTypeUuid,
            'createdAt' => $this->createdAt,
            'changedAt' => $this->changedAt,
        ]);

        $query->orderBy(['changedAt' => SORT_DESC]);

        return $dataProvider;
    }
}
