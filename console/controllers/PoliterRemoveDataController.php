<?php

namespace console\controllers;

use common\models\Measure;
use common\models\MeasureChannel;
use Exception;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\db\StaleObjectException;

/**
 * Class PoliterRemoveDataController
 * @package console\controllers
 */
class PoliterRemoveDataController extends Controller
{
    public $defaultAction = 'run';

    /**
     * @return int
     * @throws Exception
     * @throws \Throwable
     */
    public function actionRun()
    {
        // 1. show channels with zero data
        //self::showEmptyChannels();
        // 2. show channels with current values and archive
        //self::showCurrentChannels();
        // 3. drop channels values more than 200 values
        self::dropOldValues();
        return ExitCode::OK;
    }

    /**
     * @throws StaleObjectException
     * @throws \Throwable
     */
    function dropOldValues()
    {
        $measures_count = Measure::find()->count('value');
        $remove_count = 0;
        $channels = MeasureChannel::find()->where(['=', 'type', 0])->all();
        foreach ($channels as $channel) {
            $count = Measure::find()->where(['measureChannelUuid' => $channel['uuid']])->count();
            if ($count > 200) {
                $measures = Measure::find()
                    ->where(['measureChannelUuid' => $channel['uuid']])
                    ->orderBy('date')
                    ->limit($count - 200)
                    ->all();
                //echo '[' . $channel['_id'] . '] remove '.($count-200).' values'. PHP_EOL;
                foreach ($measures as $measure) {
                    $measure->delete();
                }
                $remove_count += $count;
                echo '[' . $channel['_id'] . '] ' . $channel->title . ' remove ' . ($count - 200) . ' / ' . $remove_count . ' rows [' . number_format($remove_count * 100 / $measures_count, 2) . '%]' . PHP_EOL;
            }
        }
    }

    /**
     * @throws Exception
     */
    function showEmptyChannels()
    {
        /** @var MeasureChannel[] $channels */
        $channels = MeasureChannel::find()->all();
        foreach ($channels as $channel) {
            $count = Measure::find()
                ->where(['measureChannelUuid' => $channel['uuid']])
                ->count('value');
            if ($count == 0) {
                echo '[' . $channel['_id'] . '] zero channel ' . $channel->title . PHP_EOL;
                $channel->deleted = 1;
                $channel->save();
            }
        }
    }

    /**
     * @throws Exception
     */
    function showCurrentChannels()
    {
        $channels = MeasureChannel::find()->all();
        foreach ($channels as $channel) {
            $measures = Measure::find()
                ->where(['measureChannelUuid' => $channel['uuid']])
                ->orderBy('date desc')
                ->limit(2)
                ->all();
            if ($measures) {
                echo '[' . $channel['_id'] . '] non empty current channel ' . $channel->title . ' ' .
                    number_format($measures[0]['value'], 2) . ' ' . $measures[0]['date'] . ' ' .
                    number_format($measures[1]['value'], 2) . ' ' . $measures[1]['date'] . PHP_EOL;
            }
        }
    }
}
