<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "measure_type"
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $title
 * @property string $createdAt
 * @property string $changedAt
 */
class MeasureType extends ActiveRecord
{
    const COMMON = 'E9ADE49A-3C31-42F8-A751-AAEB890C2190';

    const POWER = '7BDB38C7-EF93-49D4-8FE3-89F2A2AEDB48';
    const ENERGY = '461CDEBF-A320-4A96-BE6E-788B3E9267CF';

    const TEMPERATURE = '54051538-38F7-44A3-A9B5-C8B5CD4A2936';
    const VOLTAGE = '29A52371-E9EC-4D1F-8BCB-80F489A96DD3';
    const FREQUENCY = '041DED21-D211-4C0B-BCD6-02E392654332';
    const CURRENT = 'E38C561F-9E88-407E-A465-83803A625627';
    const STATUS = 'E45EA488-DB97-4D38-9067-6B4E29B965F8';
    const RSSI = '06F2D619-CB5A-4561-82DF-4C87DF06C6FE';

    const HEAT_CONSUMED = '1B73A000-D1E3-475B-98ED-78E05894023C';
    const HEAT_IN = '5A0A58B0-7BD8-47C8-AF09-A693E3BBAF8E';
    const HEAT_OUT = 'C1114DE7-D19F-4CF1-9876-64697E199AF5';

    const PRESSURE = '9270E308-6125-42D3-93AD-A976E3DD5D2F';

    const COLD_WATER = '4F7BC1D4-EA62-400F-9EC2-1BA289C7FCE2';
    const HOT_WATER = '40876BDE-4933-443E-B3D1-5E8610DE8E24';

    const MEASURE_TYPE_CURRENT = 0;
    const MEASURE_TYPE_HOUSE = 1;
    const MEASURE_TYPE_DAYS = 2;
    const MEASURE_TYPE_MONTH = 4;
    const MEASURE_TYPE_INTERVAL = 9;
    const MEASURE_TYPE_TOTAL = 7;
    const MEASURE_TYPE_TOTAL_CURRENT = 10;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'measure_type';
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
