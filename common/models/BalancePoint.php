<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "balance_point".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $measureChannelUuid
 * @property string $objectUuid
 * @property integer $input
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property Objects $object
 * @property MeasureChannel $measureChannel
 */
class BalancePoint extends ActiveRecord
{
    const CONSUMER = 0;
    const INPUT = 1;
    const OUTPUT = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'balance_point';
    }

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
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['input'], 'integer'],
            [['uuid', 'objectUuid', 'measureChannelUuid'], 'string', 'max' => 50],
            [['uuid', 'objectUuid', 'measureChannelUuid'], 'filter', 'filter' => function ($param) {
                return htmlspecialchars($param, ENT_QUOTES | ENT_HTML401);
            }
            ],
            [
                ['objectUuid'],
                'exist',
                'targetAttribute' => ['objectUuid' => 'uuid'],
                'targetClass' => Objects::class,
            ],
            [
                ['measureChannelUuid'],
                'exist',
                'targetAttribute' => ['measureChannelUuid' => 'uuid'],
                'targetClass' => MeasureChannel::class,
            ],
        ];
    }

    /**
     * Свойства объекта со связанными данными.
     *
     * @return array
     */
    public function fields()
    {
        return ['_id', 'uuid',
            'measureChannelUuid',
            'measureChannelUuid' => function ($model) {
                return $model->measureChannel;
            },
            'objectUuid',
            'object' => function ($model) {
                return $model->object;
            },
            'input',
            'createdAt', 'changedAt'
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', '№'),
            'uuid' => Yii::t('app', 'Uuid'),
            'measureChannelUuid' => Yii::t('app', 'Канал измерения'),
            'objectUuid' => Yii::t('app', 'Объект'),
            'measureChannel' => Yii::t('app', 'Канал измерения'),
            'object' => Yii::t('app', 'Объект'),
            'input' => Yii::t('app', 'Ввод/вывод'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * Объект связанного поля.
     *
     * @return ActiveQuery
     */
    public function getMeasureChannel()
    {
        return $this->hasOne(
            MeasureChannel::class, ['uuid' => 'measureChannelUuid']
        );
    }

    /**
     * Объект связанного поля.
     *
     * @return ActiveQuery
     */
    public function getObject()
    {
        return $this->hasOne(
            Objects::class, ['uuid' => 'objectUuid']
        );
    }
}
