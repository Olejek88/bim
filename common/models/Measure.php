<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "measure".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $measureChannelUuid
 * @property double $value
 * @property string $date
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property MeasureChannel $measureChannel
 */
class Measure extends ActiveRecord
{
    /**
     * Название таблицы
     *
     * @inheritdoc
     *
     * @return string
     */
    public static function tableName()
    {
        return 'measure';
    }

    /**
     * Behaviors
     *
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'createdAt',
                'updatedAtAttribute' => 'changedAt',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * Rules
     *
     * @inheritdoc
     *
     * @return array
     */
    public function rules()
    {
        return [
            [
                [
                    'uuid',
                    'measureChannelUuid',
                    'value',
                    'date'
                ],
                'required'
            ],
            [['value'], 'number'],
            [['uuid', 'measureChannelUuid', 'date'], 'string', 'max' => 50],
            [['oid', 'createdAt', 'changedAt'], 'safe']
        ];
    }

    /**
     * Названия отрибутов
     *
     * @inheritdoc
     *
     * @return array
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', '№'),
            'uuid' => Yii::t('app', 'Uuid'),
            'measureChannel' => Yii::t('app', 'Канал измерения'),
            'measureChannelUuid' => Yii::t('app', 'Канал измерения'),
            'value' => Yii::t('app', 'Значение'),
            'date' => Yii::t('app', 'Дата'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * Fields
     *
     * @return array
     */
    public function fields1()
    {
        return ['_id', 'uuid',
            'measureChannelUuid',
            'measureChannel' => function ($model) {
                return $model->measureChannel;
            },
            'value',
            'date',
            'createdAt',
            'changedAt',
        ];
    }

    /**
     * Объект связанного поля.
     *
     * @return ActiveQuery
     */
    public function getMeasureChannel()
    {
        return $this->hasOne(MeasureChannel::class, ['uuid' => 'sensorChannelUuid']);
    }
}
