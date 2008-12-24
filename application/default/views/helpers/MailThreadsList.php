<?php
/**
 * Books social network
 *
 * LICENSE: Closed source
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id$
 */

/**
 * Helper for writing mail threads lists
 */
class App_View_Helper_MailThreadsList extends Zend_View_Helper_Abstract
{
    /**
     * List of threads
     *
     * @param array $threads array of App_Mail_Thread
     */
    public function mailThreadsList($threads) {
        $result = '<table>';
        if (!is_array($threads) || count($threads) == 0) {
            $result .= '<tr><td>Писем нет</td></tr>';
        } else {
            foreach ($threads as $thread) {
                $result .= '<tr><td>' . $thread->getSubject() . '</td></tr>';
            }
        }
        $result .= '</table>';

        return $result;
    }
}
