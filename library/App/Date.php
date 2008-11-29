<?php
/**
 * Books social network
 *
 * LICENSE: Closed source
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id:$
 */

/**
 * Date object with additional methods
 *
 * @copyright  2008 Maxim Dikun
 * @version    Release: 0.0.1
 */
class App_Date extends Zend_Date
{
    /**
     * Returns string, which used in MySQL.
     *
     * @return string
     */
    public function toMysqlString()
    {
        return $this->toString("Y-M-d H:m:s");
    }

    // TODO write new toString, which will allow full localized dates (instead of medium)

    /**
     * Returns the actual date as new date object
     *
     * @param  string|Zend_Locale        $locale  OPTIONAL Locale for parsing input
     * @return App_Date
     */
    public static function now($locale = null)
    {
        return new App_Date(time(), Zend_Date::TIMESTAMP, $locale);
    }

    /**
     * Generates App_Date from MySQL date string
     *
     * @param string $string
     * @return App_Date
     */
    public static function fromMysqlString($string) {
        if (!is_string($string)) {
            throw new App_Date_Exception("fromMysqlString must be used with string");
        }
        return new App_Date($string, 'Y-M-d H:m:s');
    }

    public function toRelativeString() {
        $date = new App_Date($this);
        $dateNow = new App_Date();
        $diff = -$date->sub($dateNow);
        if ($diff < 60) {
            return "только что";
        } elseif ($diff < 120) {
            return "1 минута назад";
        } elseif ($diff < 300) {
            return floor($diff/60) . " минуты назад";
        } elseif ($diff < 1200) {
            return floor($diff/60) . " минут назад";
        } elseif ($diff < 3600) {
            return floor($diff/300) . " минут назад";
        } elseif ($diff < 7200) {
            return "1 час назад";
        } elseif ($diff < 18000) {
            return floor($diff/3600) . " часа назад";
        } elseif ($diff < 75600) {
            return floor($diff/3600) . " часов назад";
        } elseif ($diff < 79200) {
            return "21 час назад";
        } elseif ($diff < 86400) {
            return floor($diff/3600) . " часов назад";
        } elseif ($this->isYesterday()) {
            return "вчера";
        } elseif ($this->compare(2, Zend_Date::DAY) == -1) {
            // TODO Неправильно начиная отсюда
            return "позавчера";
        } else {
            return "Еще старше";
        }
        //return $diff." ".$this->toString();
    }
}