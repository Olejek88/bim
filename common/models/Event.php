<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "event".
 *
 * @property int $_id [int(11)]
 * @property string $uuid
 * @property string $title
 * @property string $description
 * @property string $objectUuid
 * @property boolean $deleted
 * @property string $createdAt [datetime]
 * @property string $changedAt [datetime]
 *
 */
class Event extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'event';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'title', 'objectUuid'], 'required'],
            [['uuid', 'objectUuid'], 'string', 'max' => 50],
            [['title', 'description'], 'string'],
            [['uuid', 'objectUuid'], 'filter', 'filter' => function ($param) {
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
            'uuid' => Yii::t('app', 'Uuid'),
            'objectUuid' => Yii::t('app', 'Объект'),
            'title' => Yii::t('app', 'Мероприятие'),
            'description' => Yii::t('app', 'Описание'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * @return array|false
     */
    public function fields()
    {
        return ['uuid', 'title', 'objectUuid', 'createdAt', 'changedAt', 'description', 'deleted'];
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