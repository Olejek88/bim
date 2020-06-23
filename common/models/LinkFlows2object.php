<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "link_flows2object".
 *
 * @property int $_id
 * @property string $uuid
 * @property int $flowId
 * @property string $objectUuid
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property Flows $flows
 * @property Object $object
 */
class LinkFlows2object extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'link_flows2object';
    }

    /**
     * {@inheritdoc}
     * @return LinkFlows2objectQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new LinkFlows2objectQuery(get_called_class());
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uuid', 'flowId', 'objectUuid'], 'required'],
            [['flowId'], 'integer'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid', 'objectUuid'], 'string', 'max' => 36],
            [['uuid'], 'unique'],
            [['objectUuid'], 'exist', 'skipOnError' => true, 'targetClass' => Object::className(), 'targetAttribute' => ['objectUuid' => 'uuid']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', 'Id'),
            'uuid' => Yii::t('app', 'Uuid'),
            'flowId' => Yii::t('app', 'Flow ID'),
            'objectUuid' => Yii::t('app', 'Object Uuid'),
            'createdAt' => Yii::t('app', 'Created At'),
            'changedAt' => Yii::t('app', 'Changed At'),
        ];
    }

    /**
     * Gets query for [[ObjectUu]].
     *
     * @return ActiveQuery|ObjectQuery
     */
    public function getObject()
    {
        return $this->hasOne(Object::className(), ['uuid' => 'objectUuid']);
    }

    /**
     * Gets query for [[ObjectUu]].
     *
     * @return ActiveQuery|ObjectQuery
     */
    public function getFlows()
    {
        // TODO это вообще будет работать? Лучше брать из локальных данных?
        return $this->hasOne(Flows::class, ['id' => 'flowId']);
    }
}
