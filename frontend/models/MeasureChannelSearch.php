<?php

namespace frontend\models;

use common\models\Group;
use common\models\MeasureChannel;
use common\models\RequestStatus;
use common\models\SensorChannel;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * MeasureChannelSearch represents the model behind the search form about `common\models\MeasureChannel`.
 */
class MeasureChannelSearch extends MeasureChannel
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['_id'], 'integer'],
            [['uuid', 'title', 'objectUuid', 'measureTypeUuid', 'path', 'original_name', 'param_id', 'type'], 'safe'],
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
        $query = MeasureChannel::find();

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
            'measureTypeUuid' => $this->measureTypeUuid,
            'type' => $this->type,
            'objectUuid' => $this->objectUuid
        ]);

        $query->andFilterWhere(['like', 'path', $this->path])
            ->andFilterWhere(['like', 'original_name', $this->original_name])
            ->andFilterWhere(['like', 'param_id', $this->param_id])
            ->andFilterWhere(['like', 'title', $this->title]);

        return $dataProvider;
    }
}
