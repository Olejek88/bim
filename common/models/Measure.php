<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Expression;

/**
 * This is the model class for table "measure".
 *
 * @property integer $_id
 * @property string $measureChannelId
 * @property double $value
 * @property string $date
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property MeasureChannel $measureChannel
 */
class Measure extends PoliterModel
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
                    'measureChannelId',
                    'value',
                    'date'
                ],
                'required'
            ],
            [['value'], 'number'],
            [['measureChannelId'], 'integer'],
            [['measureChannelId'],
                'exist',
                'targetAttribute' => ['measureChannelId' => '_id'],
                'targetClass' => MeasureChannel::class,
            ],
            [['date'], 'string', 'max' => 50],
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
            'measureChannel' => Yii::t('app', 'Канал измерения'),
            'measureChannelId' => Yii::t('app', 'Канал измерения'),
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
        return [
            '_id',
            'measureChannelId',
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
        return $this->hasOne(MeasureChannel::class, ['_id' => 'measureChannelId']);
    }

}
