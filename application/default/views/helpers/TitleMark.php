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
 * Helper for writing title mark
 */
class App_View_Helper_TitleMark extends Zend_View_Helper_Abstract
{
    /**
     * Array with all available marks
     *
     * @var array
     */
    protected static $_marks = array(
        -2 => "Отвратительно",
        -1 => "Плохо",
        0 => "Посредственно",
        1 => "Хорошо",
        2 => "Превосходно"
    );

    /**
     * Returns mark selection html-string
     *
     * @param App_Library_Title $title
     *
     * @return string
     */
    public function titleMark(App_Library_Title $title)
    {
        $result = '<p id="title-mark-' . $title->getId() . '"class="title-mark">Оценка: ';

        foreach (self::$_marks as $mark => $markName) {
            $result .= '<a href="#" class="mark' . $mark . '" title="' . $markName . '">&nbsp;'
                . $mark . '&nbsp;</a>';
        }

        $result .= '</p>';

        return $result;
    }
}