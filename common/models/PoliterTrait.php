<?php


namespace common\models;


trait PoliterTrait
{
    /**
     * Заменяет запятую на точку
     *
     * @param $value string
     * @return string
     */
    public static function getFloatValue($value)
    {
        return str_replace(',', '.', $value);
    }

    /**
     * Возвращает строку с датой в формате 'Y-m-d H:i:s'
     *
     * @param $datetime string
     * @return false|string
     */
    public static function getDatetime($datetime)
    {
        $pDate = date_parse_from_format('d.m.y H:i:s,v', $datetime);
        return date('Y-m-d H:i:s', mktime($pDate['hour'], $pDate['minute'], $pDate['second'], $pDate['month'],
            $pDate['day'], $pDate['year']));
    }
}