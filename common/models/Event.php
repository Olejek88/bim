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
 * @property string $date [datetime]
 * @property int $status
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
            [['uuid', 'objectUuid'], 'string', 'max' => 50],
            [['title', 'description'], 'string'],
            [['int'], 'integer'],
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
            'date' => Yii::t('app', 'Дата назначения'),
            'status' => Yii::t('app', 'Статус'),
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
        return ['uuid', 'title', 'date', 'status', 'objectUuid', 'createdAt', 'changedAt', 'description', 'deleted'];
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
     * @return array
     */
    public function getPermissions()
    {
        $class = explode('\\', get_class($this));
        $class = $class[count($class) - 1];

        $perm = parent::getPermissions();
        $perm['list'] = 'list' . $class;
        return $perm;
    }
}