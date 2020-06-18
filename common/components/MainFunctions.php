<?php

namespace common\components;

use common\models\User;
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
     * @return integer код ошибкиы
     */
    public static function register($description)
    {
        $accountUser = Yii::$app->user->identity;
        $currentUser = User::find()
            ->where(['id' => $accountUser['id']])
            ->asArray()
            ->one();
        /*        $journal = new Journal();
                $journal->userUuid = $currentUser['uuid'];
                $journal->description = $description;
                $journal->date = date('Y-m-d H:i:s');
                if ($journal->save())
                    return Errors::OK;
                else {
                    return Errors::ERROR_SAVE;
                }*/
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
     * @param $str
     */
    static function logs($str)
    {
        $handle = fopen("1.txt", "r+");
        fwrite($handle, $str);
        fclose($handle);
    }
}

