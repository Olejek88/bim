<?php

namespace frontend\models;

use common\models\Alarm;
use kartik\daterange\DateRangeBehavior;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * AlarmSearch represents the model behind the search form about `common\models\Alarm`.
 */
class AlarmSearch extends Alarm
{
    public $createTimeRange;
    public $createTimeStart;
    public $createTimeEnd;

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => DateRangeBehavior::class,
                'attribute' => 'createTimeRange',
                'dateStartAttribute' => 'createTimeStart',
                'dateEndAttribute' => 'createTimeEnd',
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'title', 'entityUuid', 'createTimeStart', 'createTimeEnd'], 'safe'],
            [['createTimeRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/']
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
        if (empty($this->createTimeStart)) {
            $today = getdate();
            $this->createTimeStart = sprintf("%d-%02d-%02d", $today['year'] - 1, $today['mon'], $today['mday']);
        }

        if (empty($this->createTimeEnd)) {
            $this->createTimeEnd = date('Y-m-d', strtotime('+3 days'));
        }

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

        $query->andFilterWhere(['>=', 'createdAt', $this->createTimeStart])
            ->andFilterWhere(['<', 'createdAt', $this->createTimeEnd]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->orderBy(['changedAt' => SORT_DESC]);

        return $dataProvider;
    }
}
