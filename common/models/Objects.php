<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Expression;

/**
 * This is the model class for table "objects".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $title
 * @property string $parentUuid
 * @property string $objectTypeUuid
 * @property string $objectSubTypeUuid
 * @property double $latitude
 * @property double $longitude
 * @property string $fiasGuid
 * @property string $fiasParentGuid
 * @property string $okato
 * @property boolean $deleted
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property ObjectType $objectType
 * @property Objects $parent
 * @property ObjectSubType $objectSubType
 */
class Objects extends PoliterModel
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
        return 'object';
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
                'value' => new Expression('NOW()')
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
                    'objectTypeUuid',
                    'title'
                ],
                'required'
            ],
            [['latitude', 'longitude'], 'number'],
            [['uuid', 'objectTypeUuid', 'parentUuid', 'objectSubTypeUuid', 'fiasGuid', 'fiasParentGuid'], 'string', 'max' => 50],
            [['createdAt', 'changedAt'], 'safe'],
            [['title', 'okato'], 'string', 'max' => 250],
            [
                [
                    'uuid',
                    'parentUuid',
                    'title',
                    'objectTypeUuid',
                    'objectSubTypeUuid',
                ],
                'filter', 'filter' => function ($param) {
                return htmlspecialchars($param, ENT_QUOTES | ENT_HTML401);
            }
            ]
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
            'uuid' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Название'),
            'parentUuid' => Yii::t('app', 'Родительский объект'),
            'objectTypeUuid' => Yii::t('app', 'Тип объекта'),
            'objectSubTypeUuid' => Yii::t('app', 'Подтип объекта'),
            'latitude' => Yii::t('app', 'Широта'),
            'longitude' => Yii::t('app', 'Долгота'),
            'fiasGuid' => Yii::t('app', 'ID ФИАС'),
            'fiasParentGuid' => Yii::t('app', 'ID Род.ФИАС'),
            'okato' => Yii::t('app', 'ОКАТО'),
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
        return ['_id', 'uuid', 'title',
            'parentUuid',
            'parent' => function ($model) {
                return $model->parent;
            },
            'objectTypeUuid',
            'objectType' => function ($model) {
                return $model->objectType;
            },
            'objectSubTypeUuid',
            'objectSubType' => function ($model) {
                return $model->objectSubType;
            },
            'latitude',
            'longitude',
            'fiasGuid',
            'fiasParentGuid',
            'okato',
            'createdAt',
            'changedAt',
        ];
    }

    /**
     * Объект связанного поля.
     *
     * @return ActiveQuery
     */
    public function getObjectType()
    {
        return $this->hasOne(ObjectType::class, ['uuid' => 'objectTypeUuid']);
    }

    /**
     * Объект связанного поля.
     *
     * @return ActiveQuery
     */
    public function getObjectSubType()
    {
        return $this->hasOne(ObjectSubType::class, ['uuid' => 'objectSubTypeUuid']);
    }

    /**
     * Объект связанного поля.
     *
     * @return ActiveQuery
     */
    public function getParent()
    {
        if ($this->parentUuid) {
            return $this->hasOne(Objects::class, ['uuid' => 'parentUuid']);
        }
        return null;
    }

    /**
     * @return array
     */
    public function getPermissions()
    {
        $class = explode('\\', get_class($this));
        $class = $class[count($class) - 1];

        $perm = parent::getPermissions();
        $perm['tree'] = 'tree' . $class;
        $perm['deleted'] = 'deleted' . $class;
        $perm['save'] = 'save' . $class;
        $perm['edit'] = 'edit' . $class;
        $perm['restore'] = 'restore' . $class;
        $perm['save-district'] = 'save-district' . $class;
        $perm['new-district'] = 'new-district' . $class;
        return $perm;
    }

    public function getFullTitle()
    {
        if ($this->objectTypeUuid == ObjectType::REGION)
            return $this->title;
        if ($this->objectTypeUuid == ObjectType::OBJECT) {
            return 'ул.' . $this->parent->title . ', ' . $this->title;
        }
        if ($this->objectTypeUuid == ObjectType::SUB_DISTRICT) {
            return $this->title;
        }
        return $this->title;
        //return 'ул.' . $house->street->title . ', д.' . $house->number . ' - ' . $this->title;
    }

    /**
     * @return string|null
     */
    public function getChildObjectType()
    {
        if ($this->objectTypeUuid == ObjectType::REGION)
            return ObjectType::DISTRICT;
        if ($this->objectTypeUuid == ObjectType::DISTRICT)
            return ObjectType::CITY;
        if ($this->objectTypeUuid == ObjectType::CITY)
            return ObjectType::CITY_DISTRICT;
        if ($this->objectTypeUuid == ObjectType::CITY_DISTRICT)
            return ObjectType::STREET;
        if ($this->objectTypeUuid == ObjectType::STREET)
            return ObjectType::OBJECT;
        if ($this->objectTypeUuid == ObjectType::OBJECT)
            return 1;
        return null;
        //return 'ул.' . $house->street->title . ', д.' . $house->number . ' - ' . $this->title;
    }

}
