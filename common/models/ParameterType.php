<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "parameter_type"
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $title
 * @property integer $type
 * @property string $createdAt
 * @property string $changedAt
 */
class ParameterType extends PoliterModel
{
    const TARGET_CONSUMPTION = '9716C884-3762-42EF-BF91-CE7B21E9D21A';
    const ENERGY_EFFICIENCY = '444CBA6D-2082-4DD7-9CC2-6EB5EBF1C5E0';
    const POWER_EQUIPMENT = '3EE2B734-B957-4191-82A7-60119C2C8556';

    const BASE_CONSUMPTION = 'D803C6CC-D567-4C37-85B7-8DBC0E43A272';
    const CONSUMPTION_COEFFICIENT = '10C48145-4EB3-43A9-9F5F-6E0CACB67CBD';

    const SQUARE = '2707720F-07DB-44BA-B25E-6DC3E9F40E7E';
    const VOLUME = '1381B5BE-D9BF-41E6-8FF2-1AEF927834CB';
    const WALL_WIDTH = '99B2881E-F250-4731-91E1-63102DE150DB';
    const KNT_HEAT_CONDUCT = 'AB8326AA-21F8-45BF-B575-BC035A3D5CB3';
    const STAGE_COUNT = '25885EBA-C790-4B10-BBF5-28F7F6CE2B67';
    const KNT_ROOF = '9E5072FA-60CD-44DC-9350-26EA77366233';
    const KNT_WINDOW = 'D5A8AD9A-8BB4-439D-9DA7-A2EF6C5C8184';
    const SUM_AVG_HEAT = '4AB9304B-4B83-4E61-AE65-2E8A2E2E2B3F';
    const PERSONAL_CNT = '6FAE233F-BD77-4E5C-B299-3276452B7001';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'parameter_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'title'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid'], 'string', 'max' => 50],
            [['title'], 'string', 'max' => 100],
        ];
    }

    /**
     * Проверка целостности модели?
     *
     * @return bool
     */
    public function upload()
    {
        if ($this->validate()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', '№'),
            'uuid' => Yii::t('app', 'Uuid'),
            'title' => Yii::t('app', 'Название'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }
}
