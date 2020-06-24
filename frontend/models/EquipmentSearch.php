<?php

namespace frontend\models;

use common\models\Equipment;
use common\models\Objects;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * EquipmentSearch represents the model behind the search form about `common\models\Equipment`.
 */
class EquipmentSearch extends Equipment
{
    public $locationUuidList;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['_id'], 'integer'],
            [
                [
                    'uuid', 'equipmentTypeUuid', 'title', 'equipmentStatusUuid', 'objectUuid', 'serial', 'createdAt', 'changedAt',
                ],
                'safe'
            ]
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
        $query = Equipment::find()->where(['deleted' => 0]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => array('pageSize' => 20),
            'sort' => [
                'defaultOrder' => ['changedAt' => SORT_DESC],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'createdAt' => $this->createdAt,
            'changedAt' => $this->changedAt,
            'equipmentTypeUuid' => $this->equipmentTypeUuid,
            'equipmentStatusUuid' => $this->equipmentStatusUuid,
            '_id' => $this->_id,
            'objectUuid' => $this->objectUuid
        ]);

        if (isset($this->objectUuid) && $this->objectUuid !== '') {
            $objectsList = Objects::find()->select('uuid, parentUuid')->asArray()->all();
            self::getChildLocationUuid($this->objectUuid, $objectsList);
        }

        $query->andFilterWhere(['like', 'uuid', $this->uuid])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'serial', $this->serial])
            ->andFilterWhere(['in', 'locationUuid', $this->locationUuidList])
            ->orderBy(['changedAt' => SORT_DESC]);

        return $dataProvider;
    }

    /**
     * Собирает дочерние $locationUuid по $uuid
     *
     * @param $uuid
     * @param $objectsList
     */
    public function getChildLocationUuid($uuid, $objectsList)
    {
        $childUuidList = [];
        $this->locationUuidList[] = $uuid;

        foreach ($objectsList as $object) {
            if ($object['parentUuid'] == $uuid) {
                $childUuidList[] = $object['uuid'];
            }
        }
        // Если есть потомки собираем рекурсивно
        if (count($childUuidList)) {
            foreach ($childUuidList as $child) {
                self::getChildLocationUuid($child, $objectsList);
            }
        }
    }
}
