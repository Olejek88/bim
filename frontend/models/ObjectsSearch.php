<?php

namespace frontend\models;

use common\models\Objects;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ObjectsSearch represents the model behind the search form about `common\models\Objects`.
 */
class ObjectsSearch extends Objects
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['_id'], 'integer'],
            [['uuid', 'title', 'parentUuid', 'objectTypeUuid', 'objectSubTypeUuid', 'latitude', 'longitude',
                'fiasGuid', 'fiasParentGuid', 'okato',
                'createdAt', 'changedAt'], 'safe'],
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
     * @throws \yii\base\InvalidConfigException
     */
    public function search($params)
    {
        $query = Objects::find();

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
            'deleted' => 0,
            'parentUuid' => $this->parentUuid,
            'objectTypeUuid' => $this->objectTypeUuid,
            'fiasGuid' => $this->fiasGuid,
            'fiasParentGuid' => $this->fiasParentGuid,
            'objectSubTypeUuid' => $this->objectSubTypeUuid
        ]);

        $query->andFilterWhere(['like', 'okato', $this->okato])
            ->andFilterWhere(['like', 'title', $this->title]);

        return $dataProvider;
    }
}
