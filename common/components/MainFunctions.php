<?php

namespace common\components;

use common\models\ActionRegister;
use common\models\User;
use dosamigos\leaflet\types\LatLng;
use Yii;

/**
 * Class MainFunctions
 */
class MainFunctions
{
    /**
     * Logs one or several messages into daemon log file.
     * @param string $filename
     * @param array|string $messages
     */
    public static function log($filename, $messages)
    {
        if (!is_array($messages)) {
            $messages = [$messages];
        }
        foreach ($messages as $message) {
            file_put_contents($filename, date('d.m.Y H:i:s') . ' - ' . $message . PHP_EOL, FILE_APPEND | LOCK_EX);
        }
    }

    /**
     * Logs message to journal register in db
     * @param string $description сообщение в журнал
     * @param $type
     * @param $entityUuid
     * @return void код ошибки
     * @throws \Exception
     */
    public static function register($description, $type, $entityUuid)
    {
        $accountUser = Yii::$app->user->identity;
        $currentUser = User::find()
            ->where(['id' => $accountUser['id']])
            ->asArray()
            ->one();
        $register = new ActionRegister();
        $register->uuid = MainFunctions::GUID();
        $register->title = $description;
        $register->userId = $currentUser['id'];
        $register->type = $type;
        $register->entityUuid = $entityUuid;
        $register->save();
    }

    /**
     * return generated UUID
     * @return string generated UUID
     * @throws \Exception
     */
    static function GUID()
    {
        if (function_exists('com_create_guid') === true) {
            return trim(com_create_guid(), '{}');
        }
        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X',
            random_int(0, 65535),
            random_int(0, 65535),
            random_int(0, 65535),
            random_int(16384, 20479),
            random_int(32768, 49151),
            random_int(0, 65535),
            random_int(0, 65535),
            random_int(0, 65535));
    }

    /**
     * Sort array by param
     * @param $array
     * @param $cols
     * @return array
     */
    public static function array_msort($array, $cols)
    {
        $colarr = array();
        foreach ($cols as $col => $order) {
            $colarr[$col] = array();
            foreach ($array as $k => $row) {
                $colarr[$col]['_' . $k] = strtolower($row[$col]);
            }
        }
        $eval = 'array_multisort(';
        foreach ($cols as $col => $order) {
            $eval .= '$colarr[\'' . $col . '\'],' . $order . ',';
        }
        $eval = substr($eval, 0, -1) . ');';
        eval($eval);
        $ret = array();
        foreach ($colarr as $col => $arr) {
            foreach ($arr as $k => $v) {
                $k = substr($k, 1);
                if (!isset($ret[$k])) $ret[$k] = $array[$k];
                $ret[$k][$col] = $array[$k][$col];
            }
        }
        return $ret;
    }

    /**
     * @param $str
     */
    static function logs($str)
    {
        $handle = fopen("1.txt", "r+");
        fwrite($handle, $str);
        fclose($handle);
    }

    /**
     * @param $p LatLng
     * @param $polygon LatLng[]
     * @return bool
     */
    static function isPointInPolygon($p, $polygon)
    {
        if (count($polygon)) {
            $minX = $polygon[0]->lat;
            $maxX = $polygon[0]->lat;
            $minY = $polygon[0]->lng;
            $maxY = $polygon[0]->lng;
            for ($i = 1; $i < count($polygon); $i++) {
                $q = $polygon[$i];
                $minX = min($q->lat, $minX);
                $maxX = max($q->lat, $maxX);
                $minY = min($q->lng, $minY);
                $maxY = max($q->lng, $maxY);
            }

            if ($p->lat < $minX || $p->lat > $maxX || $p->lng < $minY || $p->lng > $maxY) {
                return false;
            }

            $inside = false;
            for ($i = 0, $j = count($polygon) - 1; $i < count($polygon); $j = $i++) {
                if (($polygon[$i]->lng > $p->lng) != ($polygon[$j]->lng > $p->lng) &&
                    $p->lat < ($polygon[$j]->lat - $polygon[$i]->lat) * ($p->lng - $polygon[$i]->lng) / ($polygon[$j]->lng - $polygon[$i]->lng) + $polygon[$i]->lat) {
                    $inside = !$inside;
                }
            }
            return $inside;
        }
        return false;
    }
}
