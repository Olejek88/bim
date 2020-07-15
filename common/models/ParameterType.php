<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "parameter_type"
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $title
 * @property string $createdAt
 * @property string $changedAt
 */
class ParameterType extends PoliterModel
{
    const TARGET_CONSUMPTION = '9716C884-3762-42EF-BF91-CE7B21E9D21A';
    const ENERGY_EFFICIENCY = '444CBA6D-2082-4DD7-9CC2-6EB5EBF1C5E0';
    const POWER_EQUIPMENT = '3EE2B734-B957-4191-82A7-60119C2C8556';

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
