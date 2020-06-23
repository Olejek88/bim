<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "object_contragent".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $districtUuid
 * @property string $objectUuid
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property Objects $object
 * @property Objects $district
 */
class ObjectDistrict extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'object_district';
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
            [['uuid', 'districtUuid', 'objectUuid'], 'string', 'max' => 50],
            [['uuid', 'districtUuid', 'objectUuid'], 'filter', 'filter' => function ($param) {
                return htmlspecialchars($param, ENT_QUOTES | ENT_HTML401);
            }
            ],
            [
                ['districtUuid'],
                'exist',
                'targetAttribute' => ['districtUuid' => 'uuid'],
                'targetClass' => Objects::class,
            ],
            [
                ['objectUuid'],
                'exist',
                'targetAttribute' => ['objectUuid' => 'uuid'],
                'targetClass' => Objects::class,
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
            'districtUuid',
            'districtUuid' => function ($model) {
                return $model->district;
            },
            'objectUuid',
            'object' => function ($model) {
                return $model->object;
            },
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
            'districtUuid' => Yii::t('app', 'Район'),
            'objectUuid' => Yii::t('app', 'Объект'),
            'district' => Yii::t('app', 'Район'),
            'object' => Yii::t('app', 'Объект'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * Проверка целостности модели
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
     * Объект связанного поля.
     *
     * @return ActiveQuery
     */
    public function getDistrict()
    {
        return $this->hasOne(
            Objects::class, ['uuid' => 'districtUuid']
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
