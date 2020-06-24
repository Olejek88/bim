<?php

namespace frontend\models;

use common\models\Alarm;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * AlarmSearch represents the model behind the search form about `common\models\Alarm`.
 */
class AlarmSearch extends Alarm
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'title', 'entityUuid'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
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
        $query = Alarm::find();

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
            'entityUuid' => $this->entityUuid
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->orderBy(['changedAt' => SORT_DESC]);

        return $dataProvider;
    }
}
