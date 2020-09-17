<?php

namespace common\models;

use common\components\MainFunctions;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "measure_channel".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $title
 * @property string $objectUuid
 * @property string $measureTypeUuid
 * @property string $createdAt
 * @property string $changedAt
 * @property boolean $deleted
 * @property string $path
 * @property string $original_name
 * @property string $param_id
 * @property integer $type
 * @property integer $status
 *
 * @property MeasureType $measureType
 * @property Objects $object
 * @property-read string $typeName
 * @property-read string $formatLastMeasure
 * @property-read array $lastMeasure
 */
class MeasureChannel extends PoliterModel
{
    const STATUS_OFF = 0; // канал отключен / не используется
    const STATUS_ACTIVE = 1; // канал включен / данные актуальны / связь есть
    const STATUS_NO_CONNECT = -1; // канал включен / данные не актуальны / связи нет

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'measure_channel';
    }

    /**
     * Behaviors.
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
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'title', 'objectUuid', 'measureTypeUuid'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid', 'objectUuid', 'measureTypeUuid', 'param_id'], 'string', 'max' => 50],
            [['type', 'status'], 'integer'],
            [['title', 'original_name'], 'string', 'max' => 250],
            [['path'], 'string', 'max' => 512],
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
            'object' => Yii::t('app', 'Объект'),
            'objectUuid' => Yii::t('app', 'Объект'),
            'measureType' => Yii::t('app', 'Тип измерения'),
            'measureTypeUuid' => Yii::t('app', 'Тип измерения'),
            'type' => Yii::t('app', 'Тип значений'),
            'status' => Yii::t('app', 'Статус канала'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getMeasureType()
    {
        return $this->hasOne(MeasureType::class, ['uuid' => 'measureTypeUuid']);
    }

    /**
     * @return ActiveQuery
     */
    public function getObject()
    {
        return $this->hasOne(Objects::class, ['uuid' => 'objectUuid']);
    }

    /**
     * @return array
     */
    public function getLastMeasure()
    {
        /** @var MeasureLast $measure */
        // вспомогательный запрос
        $lastMeasureTmp = MeasureLast::find()
            ->select('measureChannelId, max(date) as date')
            ->where([
                'measureChannelId' => $this->_id,
            ])
            ->groupBy('measureChannelId');
        // выбираем из базы для каждого канала измерений последнее по дате значение
        $measure = Measure::find()
            ->with(['measureChannel'])
            ->innerJoin(['m' => $lastMeasureTmp], 'measure.measureChannelId=m.measureChannelId and measure.date=m.date')
            ->asArray()
            ->all();

        return $measure;
    }

    /**
     * @return string
     */
    public function getFormatLastMeasure()
    {
        $text = '-';
        $measures = $this->getLastMeasure();
        if (count($measures) > 0) {
            $text = '';
            foreach ($measures as $measure) {
                if ($text != '') {
                    $text .= '<br/>';
                }

                $text .= number_format($measure['value'], 3)
                    . ' [' . date("m/d h:i:s", strtotime($measure['date'])) . ']';
            }
        }

        return $text;
    }

    /**
     * @param $channel int|array
     * @return array
     */
    public static function getLastMeasureStatic($channel)
    {
        /** @var MeasureLast $measure */
        // вспомогательный запрос
        $lastMeasureTmp = MeasureLast::find()
            ->select('measureChannelId, max(date) as date')
            ->where([
                'measureChannelId' => $channel,
            ])
            ->groupBy('measureChannelId');
        // выбираем из базы для каждого канала измерений последнее по дате значение
        $measure = Measure::find()
            ->with(['measureChannel'])
            ->innerJoin(['m' => $lastMeasureTmp], 'measure.measureChannelId=m.measureChannelId and measure.date=m.date')
            ->asArray()
            ->all();

        return $measure;
    }

    /**
     * @param $channel int|array
     * @return string
     */
    public static function getFormatLastMeasureStatic($channel)
    {
        $text = '';
        $measures = self::getLastMeasureStatic($channel);
        if (count($measures)) {
            foreach ($measures as $measure) {
                if ($text != '') {
                    $text .= '<br/>';
                }
                $text .= number_format($measure['value'], 3, ".", "")
                    . ' [' . date("m/d h:i", strtotime($measure['date'])) . ']';
            }
        }

        return $text;
    }

    /**
     * @return string
     */
    public function getTypeName()
    {
        if ($this->type == MeasureType::MEASURE_TYPE_CURRENT)
            return 'Текущее';
        if ($this->type == MeasureType::MEASURE_TYPE_HOURS)
            return 'Часовое';
        if ($this->type == MeasureType::MEASURE_TYPE_DAYS)
            return 'Дневное';
        if ($this->type == MeasureType::MEASURE_TYPE_MONTH)
            return 'По месяцам';
        if ($this->type == MeasureType::MEASURE_TYPE_TOTAL)
            return 'На дату';
        if ($this->type == MeasureType::MEASURE_TYPE_INTERVAL)
            return 'Интервальное';
        if ($this->type == MeasureType::MEASURE_TYPE_TOTAL_CURRENT)
            return 'Текущий итог';
        return 'Неизвестен';
    }

    /**
     * @return array
     */
    public function getPermissions()
    {
        $class = explode('\\', get_class($this));
        $class = $class[count($class) - 1];

        $perm = parent::getPermissions();
        $perm['dashboard'] = 'dashboard' . $class;
        $perm['trend'] = 'trend' . $class;
        return $perm;
    }

    /**
     * @param $objectUuid
     * @param $measureTypeUuid
     * @param $type
     * @return ActiveRecord
     */
    public static function getChannel($objectUuid, $measureTypeUuid, $type)
    {
        return MeasureChannel::find()
            ->where(['objectUuid' => $objectUuid])
            ->andWhere(['measureTypeUuid' => $measureTypeUuid])
            ->andWhere(['type' => $type])
            ->limit(1)
            ->one();
    }

    /**
     * @param $uuid
     * @param $date
     * @return Parameter|null
     */
    public function getParameter($uuid, $date)
    {
        /** @var Parameter $parameter */
        $parameter = Parameter::find()
            ->where(['entityUuid' => $this->uuid])
            ->andWhere(['date' => $date])
            ->andWhere(['parameterTypeUuid' => $uuid])
            ->limit(1)
            ->one();
        if ($parameter) {
            return $parameter;
        }
        return null;
    }

    /**
     * @param $type
     * @return Parameter|null
     * @throws \Exception
     */
    public function createNewChannel($type)
    {
        $object = Objects::find()->where(['objectTypeUuid' => ObjectType::CITY])->andWhere(['deleted' => 0])->limit(1)->one();
        if ($object) {
            $measureChannel = new MeasureChannel();
            $measureChannel->uuid = MainFunctions::GUID();
            $measureChannel->title = 'Температура наружного воздуха';
            $measureChannel->objectUuid = $object['uuid'];
            $measureChannel->measureTypeUuid = MeasureType::TEMPERATURE_AIR;
            $measureChannel->deleted = 0;
            $measureChannel->path = 'external';
            $measureChannel->original_name = '';
            $measureChannel->param_id = 0;
            $measureChannel->type = $type;
            $measureChannel->status = 1;
            $measureChannel->save();
        }
        return null;
    }
}
