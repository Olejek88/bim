<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "object_sub_type".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $title
 * @property string $createdAt
 * @property string $changedAt
 */
class ObjectSubType extends PoliterModel
{
    const GENERAL = "42686CFC-34D0-45FF-95A4-04B0D865EC35";
    const MKD = "6A131086-AEAA-4513-8C9D-3CEAA979A2EC";
    const COMMERCE = "587B526B-A5C2-4B30-92DD-C63F796333A6";
    const INPUT = "F68A562B-8F61-476F-A3E7-5666F9CEAFA1";
    const PLACEMENT = "61CBB37E-0BC2-4713-B101-50E9F84F46B4";

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%object_type}}';
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
            [['uuid', 'title'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid', 'title'], 'string', 'max' => 50],
        ];
    }

    public function fields1()
    {
        return [
            '_id',
            'uuid',
            'title',
            'createdAt',
            'changedAt',
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
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }
}
