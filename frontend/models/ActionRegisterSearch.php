<?php

namespace frontend\models;

use common\models\ActionRegister;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ActionRegisterSearch represents the model behind the search form about `common\models\ActionRegister`.
 */
class ActionRegisterSearch extends ActionRegister
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'userId', 'type'], 'safe']
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
        $query = ActionRegister::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['createdAt' => SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'userId' => $this->userId,
            'entityUuid' => $this->entityUuid,
            'type' => $this->type
        ]);

        return $dataProvider;
    }
}
