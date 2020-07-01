<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Expression;

/**
 * This is the model class for table "attribute".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $attributeTypeUuid
 * @property string $title
 * @property string $entityUuid
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property AttributeType $attributeType
 */
class Attribute extends PoliterModel
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
        return 'attribute';
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
                    'attributeTypeUuid',
                    'entityUuid',
                    'title'
                ],
                'required'
            ],
            [['uuid', 'attributeTypeUuid', 'entityUuid'], 'string', 'max' => 50],
            [['title'], 'string', 'max' => 50],
            [['createdAt', 'changedAt'], 'safe']
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
            'attributeType' => Yii::t('app', 'Тип атрибута'),
            'attributeTypeUuid' => Yii::t('app', 'Тип атрибута'),
            'entity' => Yii::t('app', 'Связанная сущность'),
            'entityUuid' => Yii::t('app', 'Связанная сущность'),
            'title' => Yii::t('app', 'Название'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * Fields
     *
     * @return array
     */
    public function fields()
    {
        return ['_id', 'uuid',
            'attributeTypeUuid',
            'attributeType' => function ($model) {
                return $model->attributeType;
            },
            'entityUuid',
            'title',
            'createdAt',
            'changedAt',
        ];
    }

    /**
     * Объект связанного поля.
     *
     * @return ActiveQuery
     */
    public function getAttributeType()
    {
        return $this->hasOne(AttributeType::class, ['uuid' => 'attributeTypeUuid']);
    }

    /**
     * Объект связанного поля.
     *
     * @return string
     */
    public function getEntityName()
    {
        if ($this->entityUuid) {
            $equipment = Equipment::find()->where(['uuid' => $this->entityUuid])->one();
            if ($equipment) {
                return $equipment['title'];
            }
            $object = Objects::find()->where(['uuid' => $this->entityUuid])->one();
            if ($object) {
                return $object['title'];
            }
        }
        return "";
    }
}
