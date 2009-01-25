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
 * model
 */
class App_View_Helper_UsersList extends Zend_View_Helper_Abstract
{
    const LIST_DEFAULT = 0;
    const LIST_SENT_REQUESTS = 1;
    const LIST_RECEIVED_REQUESTS = 2;

    /**
     * List of users
     *
     * @param array $users array of App_User
     */
    public function usersList(array $users = array(), $listType = self::LIST_DEFAULT, $emptyMessage = 'Нет друзей')
    {
        if (count($users) == 0) {
            return $emptyMessage;
        }
        $result = '<table class="users">';

        $i = 0;
        foreach ($users as $user) {
            $userFriendState = $user->getFriendState();
            if ($i%3 === 0) {
                $result .= '<tr>';
            }
            $result .= '<td>'
                    .  '<table><tr><td>'
                    .  $this->view->profileLink($user) . '</td><td><ul>';


            if ($listType !== self::LIST_SENT_REQUESTS && $listType !== self::LIST_RECEIVED_REQUESTS
                && $userFriendState !== null && $userFriendState !== App_User_Friends::STATE_REQUEST_RECEIVED
                && $userFriendState !== App_User_Friends::STATE_REQUEST_SENT) {

                $result .= '<li><a href="'
                        . $this->view->url(array(
                              'action' => ($userFriendState === App_User_Friends::STATE_APPROVED
                                    ? 'confirm-delete' : 'confirm'),
                              'user' => $user->getLogin()
                          ), 'friends')
                        . '">'
                        . ($userFriendState === App_User_Friends::STATE_APPROVED
                            ? 'Убрать из друзей' : 'Добавить в друзья')
                        . '</a></li>';
            }
            if ($listType === self::LIST_RECEIVED_REQUESTS) {
                $result .= '<li><a href="'
                        . $this->view->url(array(
                              'action' => 'accept',
                              'user' => $user->getLogin()
                          ), 'friends')
                        . '">Подтвердить</a></li>'
                        . '<li><a href="'
                        . $this->view->url(array(
                              'action' => 'decline',
                              'user' => $user->getLogin()
                          ), 'friends')
                        . '">Отменить</a></li>';
            }
            if ($listType === self::LIST_SENT_REQUESTS) {
                $result .= '<li><a href="'
                        . $this->view->url(array(
                              'action' => 'cancel',
                              'user' => $user->getLogin()
                          ), 'friends')
                        . '">Отменить</a></li>';
            }
            $result .= '<li><a href="'
                    . $this->view->url(array(
                          'action' => 'new'
                      ), 'mail')
                    . '?to=' . $user->getLogin() . '">Написать сообщение</a></li>'
                    .  '</ul></td></tr></table>'
                    .  '</td>';
            ++$i;
            if ($i%3 === 0) {
                $result .= '</tr>';
            }
        }

        if ($i%3 !== 0) {
            $result .= '</tr>';
        }

        $result .= '</table>';

        return $result;
    }
}
