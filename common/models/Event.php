<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "event".
 *
 * @property int $_id [int(11)]
 * @property string $uuid
 * @property string $title
 * @property string $description
 * @property string $objectUuid
 * @property string $eventTypeUuid
 * @property string $date [datetime]
 * @property string $dateFact [datetime]
 * @property int $status
 * @property double $cnt_coverage
 * @property boolean $deleted
 * @property string $createdAt [datetime]
 * @property string $changedAt [datetime]
 *
 */

class Event extends PoliterModel
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
            [['uuid', 'title', 'objectUuid', 'date'], 'required'],
            [['uuid', 'objectUuid', 'eventTypeUuid'], 'string', 'max' => 50],
            [['title', 'description', 'dateFact'], 'string'],
            [['status'], 'integer'],
            [['cnt_coverage'], 'double'],
            [['uuid', 'objectUuid', 'eventTypeUuid'], 'filter', 'filter' => function ($param) {
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
            'date' => Yii::t('app', 'Дата назначения'),
            'dateFact' => Yii::t('app', 'Дата выполнения'),
            'status' => Yii::t('app', 'Статус'),
            'title' => Yii::t('app', 'Мероприятие'),
            'description' => Yii::t('app', 'Описание'),
            'cnt_coverage' => Yii::t('app', 'Коэффициент охвата'),
            'eventTypeUuid' => Yii::t('app', 'Тип'),
            'eventType' => Yii::t('app', 'Тип'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * @return array|false
     */
    public function fields()
    {
        return ['uuid', 'title', 'date', 'dateFact', 'status', 'objectUuid', 'eventTypeUuid',
            'createdAt', 'changedAt', 'description', 'cnt_coverage', 'deleted'];
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

    /**
     * Объект связанного поля.
     *
     * @return ActiveQuery
     */
    public function getEventType()
    {
        return $this->hasOne(
            EventType::class, ['uuid' => 'eventTypeUuid']
        );
    }

    /**
     * @return array
     */
    public function getPermissions()
    {
        $class = explode('\\', get_class($this));
        $class = $class[count($class) - 1];

        $perm = parent::getPermissions();
        $perm['list'] = 'list' . $class;
        $perm['plan'] = 'plan' . $class;
        $perm['calendar'] = 'calendar' . $class;
        $perm['jsoncalendar'] = 'jsoncalendar' . $class;
        $perm['change'] = 'change' . $class;
        $perm['plan-edit'] = 'plan-edit' . $class;
        return $perm;
    }
}