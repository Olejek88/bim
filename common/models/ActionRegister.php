<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Expression;

/**
 * This is the model class for table "action_register".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $title
 * @property integer $userId
 * @property integer $type
 * @property string $entityUuid
 * @property string $createdAt
 * @property string $changedAt
 */
class ActionRegister extends PoliterModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'action_register';
    }

    /**
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
            [['uuid'], 'required'],
            [['title'], 'string'],
            [['createdAt', 'changedAt'], 'safe'],
            [['userId', 'type'], 'integer'],
            [['uuid', 'entityUuid'], 'string', 'max' => 45],
        ];
    }

    /**
     * @return array|false
     */
    public function fields()
    {
        return ['_id', 'uuid', 'userId', 'entityUuid', 'userId'];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', '№'),
            'uuid' => Yii::t('app', 'Uuid'),
            'title' => Yii::t('app', 'Событие'),
            'userId' => Yii::t('app', 'Исполнитель'),
            'type' => Yii::t('app', 'Тип события'),
            'entityUuid' => Yii::t('app', 'Сущность'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * @return string
     */
    public function getEntityName()
    {
        if ($this->entityUuid) {
            /** @var Equipment $equipment */
            $equipment = Equipment::find()->where(['uuid' => $this->entityUuid])->one();
            if ($equipment) {
                return $equipment->title;
            }
        }
        return "-";
    }

    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'userId']);
    }
}
