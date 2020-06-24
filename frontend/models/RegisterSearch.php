<?php

namespace frontend\models;

use common\models\Register;
use kartik\daterange\DateRangeBehavior;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * RegisterSearch represents the model behind the search form about `common\models\Register`.
 */
class RegisterSearch extends Register
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
            [['entityUuid', 'title', 'type', 'createdAt', 'createTimeStart', 'createTimeEnd'], 'safe'],
            [['createTimeRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
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
        $query = Register::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['createdAt' => SORT_DESC]]
        ]);

        if (empty($this->createTimeStart)) {
            $today = getdate();
            $this->createTimeStart = sprintf("%d-%02d-%02d", $today['year'] - 1, $today['mon'], $today['mday']);
        }

        if (empty($this->createTimeEnd)) {
            $this->createTimeEnd = date('Y-m-d', strtotime('+3 days'));
        }

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'entityUuid' => $this->entityUuid,
            'type' => $this->type
        ]);

        $query->andFilterWhere(['>=', 'createdAt', $this->createTimeStart])
            ->andFilterWhere(['<', 'createdAt', $this->createTimeEnd]);

        $query->andFilterWhere(['like', 'title', $this->title]);

        return $dataProvider;
    }
}
