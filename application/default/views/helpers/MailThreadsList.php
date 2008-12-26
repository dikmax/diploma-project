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
        $result = '<table class="mail">';
        $currentUserId = App_User_Factory::getSessionUser()->getId();
        if (!is_array($threads) || count($threads) == 0) {
            $result .= '<tr><td colspan="3">Писем нет</td></tr>';
        } else {
            $filter = new Zend_Filter_HtmlEntities(ENT_COMPAT, 'UTF-8');
            foreach ($threads as $thread) {
                $user = $thread->getUser1Id() == $currentUserId
                           ? $thread->getUser2()
                           : $thread->getUser1();
                $result .= '<tr>'
                        . '<td>' . $this->view->profileLink($user) . '</td>'
                        . '<td>'
                        .     '<a href="'. $this->view->url(array('param' => $thread->getId())) . '#new-message">'
                        .     $filter->filter($thread->getSubject())
                        .     '</a>'
                        . '</td>'
                        . '<td>' . $thread->getDate() . '</td>'
                        . '</tr>';
            }
        }
        $result .= '</table>';

        return $result;
    }
}
