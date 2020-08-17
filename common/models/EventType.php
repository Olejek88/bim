<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Expression;

/**
 * This is the model class for table "event_type".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $title
 * @property double $cnt_effect
 * @property int $source
 * @property string $parameterTypeUuid
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property ParameterType $parameterType
 */
class EventType extends PoliterModel
{
    const COMMON = '3607A2C5-4930-4F92-A1C5-CC42131A3237';

    /**
     * Название таблицы.
     *
     * @return string
     *
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'event_type';
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
     * Rules.
     *
     * @return array
     *
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'title'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid', 'parameterTypeUuid'], 'string', 'max' => 50],
            [['source'], 'integer'],
            [['cnt_effect'], 'double'],
            [['title'], 'string', 'max' => 100],
            [['uuid', 'title'], 'filter', 'filter' => function ($param) {
                return htmlspecialchars($param, ENT_QUOTES | ENT_HTML401);
            }
            ],

        ];
    }

    /**
     * Labels.
     *
     * @return array
     *
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', '№'),
            'uuid' => Yii::t('app', 'Uuid'),
            'title' => Yii::t('app', 'Название'),
            'source' => Yii::t('app', 'Энергоресурс'),
            'cnt_effect' => Yii::t('app', 'Коэффициент влияния'),
            'parameterTypeUuid' => Yii::t('app', 'Тип параметра'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * Объект связанного поля.
     *
     * @return ActiveQuery
     */
    public function getParameterType()
    {
        if ($this->parameterTypeUuid) {
            return $this->hasOne(
                ParameterType::class, ['uuid' => 'parameterTypeUuid']
            );
        } else
            return null;
    }
}
