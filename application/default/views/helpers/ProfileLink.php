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
 * Helper for linking inside library
 */
class App_View_Helper_ProfileLink extends Zend_View_Helper_Abstract
{
    /**
     * Returns profile link
     *
     * @param App_User|string $user
     *
     * @return string
     */
    public function profileLink($user)
    {
        $params = array('action' => 'profile');

        $login = $user instanceof App_User
            ? $user->getLogin()
            : $user;
        $params['login'] = $login;

        return '<a href="' . $this->view->url($params, 'user') . '" title="Посмотреть профиль ' . $login . '">'
            . $login
            . '</a>';
    }
}