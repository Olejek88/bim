<?php

namespace frontend\models;

use common\models\Attribute;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * AttributeSearch represents the model behind the search form about `common\models\Attribute`.
 */
class AttributeSearch extends Attribute
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'title', 'attributeTypeUuid', 'entityUuid'], 'safe'],
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
        $query = Attribute::find();

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
            'attributeTypeUuid' => $this->attributeTypeUuid,
            'entityUuid' => $this->entityUuid,
            'createdAt' => $this->createdAt,
            'changedAt' => $this->changedAt,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->orderBy(['changedAt' => SORT_DESC]);

        return $dataProvider;
    }
}
