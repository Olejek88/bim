<?php

namespace common\models;

use common\components\MainFunctions;
use dosamigos\leaflet\types\LatLng;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
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
 * @property integer $source
 * @property integer $water
 * @property integer $electricity
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
            [['latitude', 'longitude', 'source', 'water', 'electricity'], 'number'],
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
            'source' => Yii::t('app', 'Поставщик'),
            'water' => Yii::t('app', 'ХВС'),
            'electricity' => Yii::t('app', 'Электроэнергия'),
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
            'source',
            'water',
            'electricity',
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
     * Объект связанного поля.
     *
     * @return string
     */
    public function getParentsTitle()
    {
        $title = '';
        $parentUuid = $this->parentUuid;
        while ($parentUuid) {
            $title .= ' / ';
            /** @var Objects $object */
            $object = Objects::find()->where(['uuid' => $parentUuid])->one();
            $title .= $object->title;
            $parentUuid = $object->parentUuid;
        }
        return $title;
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
        $perm['plan'] = 'plan' . $class;
        $perm['restore'] = 'restore' . $class;
        $perm['save-district'] = 'save-district' . $class;
        $perm['new-district'] = 'new-district' . $class;
        $perm['dashboard'] = 'dashboard' . $class;
        $perm['districts'] = 'districts' . $class;
        $perm['plan-edit'] = 'plan-edit' . $class;
        $perm['table'] = 'table' . $class;
        $perm['target'] = 'target' . $class;
        $perm['parameter-edit'] = 'parameter-edit' . $class;
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

    /**
     * @param $max_count
     * @return array
     */
    public function getObjectsByDistrict($max_count)
    {
        $objectsIn = [];
        $cnt = 0;
        if ($this->objectTypeUuid == ObjectType::SUB_DISTRICT) {
            $coordinates = DistrictCoordinates::find()->where(['districtUuid' => $this->uuid])->one();
            if ($coordinates) {
                $objects = Objects::find()->where(['objectTypeUuid' => ObjectType::OBJECT])->all();
                foreach ($objects as $object) {
                    $p = new LatLng(['lat' => $object->latitude, 'lng' => $object->longitude]);
                    if (MainFunctions::isPointInPolygon($p, json_decode($coordinates['coordinates']))) {
                        $objectsIn[] = $object;
                        if ($cnt++ >= $max_count - 1) break;
                    }
                }
            }
        }
        return $objectsIn;
    }

    /**
     * @return array|ActiveRecord
     */
    public function getSubDistrict()
    {
        if ($this->objectTypeUuid == ObjectType::OBJECT) {
            $districtCoordinates = DistrictCoordinates::find()->all();
            $p = new LatLng(['lat' => $this->latitude, 'lng' => $this->longitude]);
            foreach ($districtCoordinates as $districtCoordinate) {
                if (MainFunctions::isPointInPolygon($p, json_decode($districtCoordinate['coordinates']))) {
                    $district = Objects::find()->where(['uuid' => $districtCoordinate['districtUuid']])->one();
                    return $district;
                }
            }
        }
        return null;
    }

    /**
     * @param $uuid
     * @return string
     */
    public function getParameter($uuid)
    {
        /** @var Parameter $parameter */
        $parameter = Parameter::find()
            ->where(['entityUuid' => $this->uuid])
            ->andWhere(['parameterTypeUuid' => $uuid])
            ->one();
        if ($parameter) {
            return $parameter->value;
        }
        return "n/a";
    }

    /**
     * @param string $uuid
     * @param boolean $includeDate
     * @return string
     */
    public function getCurrent($uuid, $includeDate)
    {
        $measureChannel = MeasureChannel::find()
            ->where(['objectUuid' => $this->uuid])
            ->andWhere(['measureTypeUuid' => $uuid])
            //->andWhere(['type' => MeasureType::CURRENT])
            ->limit(1)
            ->one();
        if ($measureChannel) {
            /** @var Measure $measure */
            $measure = Measure::find()
                ->where(['measureChannelUuid' => $measureChannel['uuid']])
                ->orderBy('date desc')
                ->limit(1)
                ->one();
            if ($measure) {
                if ($includeDate)
                    return number_format($measure->value, 3) . ' [' . $measure->date . ']';
                else
                    return number_format($measure->value, 3);
            }
        }
        return "-";
    }
}
