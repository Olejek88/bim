<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Expression;

/**
 * This is the model class for table "measure_channel".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $title
 * @property string $objectUuid
 * @property string $measureTypeUuid
 * @property string $createdAt
 * @property string $changedAt
 * @property boolean $deleted
 * @property string $path
 * @property string $original_name
 * @property string $param_id
 * @property integer $type
 *
 * @property MeasureType $measureType
 * @property Objects $object
 */
class MeasureChannel extends PoliterModel
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'measure_channel';
    }

    /**
     * Behaviors.
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
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'title', 'objectUuid', 'measureTypeUuid'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid', 'objectUuid', 'measureTypeUuid', 'param_id'], 'string', 'max' => 50],
            [['type'], 'integer'],
            [['title', 'original_name'], 'string', 'max' => 250],
            [['path'], 'string', 'max' => 512],
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
            'title' => Yii::t('app', 'Название'),
            'object' => Yii::t('app', 'Объект'),
            'objectUuid' => Yii::t('app', 'Объект'),
            'measureType' => Yii::t('app', 'Тип измерения'),
            'measureTypeUuid' => Yii::t('app', 'Тип измерения'),
            'type' => Yii::t('app', 'Тип значений'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getMeasureType()
    {
        return $this->hasOne(MeasureType::class, ['uuid' => 'measureTypeUuid']);
    }

    /**
     * @return ActiveQuery
     */
    public function getObject()
    {
        return $this->hasOne(Objects::class, ['uuid' => 'objectUuid']);
    }

    /**
     * @return string
     */
    public function getLastMeasure()
    {
        /** @var Measure $measure */
        $measure = Measure::find()
            ->where(['measureChannelUuid' => $this->uuid])
            ->orderBy('date desc')
            ->limit(1)
            ->one();
        if ($measure) {
            return $measure->value . ' [' . date("Y-m-d h:i:s", $measure->date) . ']';
        }
        return '-';
    }

    /**
     * @return string
     */
    public function getTypeName()
    {
        if ($this->type == MeasureType::MEASURE_TYPE_CURRENT)
            return 'Текущее';
        if ($this->type == MeasureType::MEASURE_TYPE_HOURS)
            return 'Часовое';
        if ($this->type == MeasureType::MEASURE_TYPE_DAYS)
            return 'Дневное';
        if ($this->type == MeasureType::MEASURE_TYPE_MONTH)
            return 'По месяцам';
        if ($this->type == MeasureType::MEASURE_TYPE_TOTAL)
            return 'На дату';
        if ($this->type == MeasureType::MEASURE_TYPE_INTERVAL)
            return 'Интервальное';
        if ($this->type == MeasureType::MEASURE_TYPE_TOTAL_CURRENT)
            return 'Текущий итог';
        return 'Неизвестен';
    }
}
