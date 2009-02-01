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
 * View with integrated helpers
 */
class App_View extends Zend_View
{
    const LIST_DEFAULT = 0;
    const LIST_SENT_REQUESTS = 1;
    const LIST_RECEIVED_REQUESTS = 2;

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
     * Check acl
     *
     * @param string $resource
     * @param string $previlege
     *
     * @return boolean
     */
    public function isAllowed($resource, $previlege)
    {
        $acl = Zend_Registry::get('acl');
        $aclRole = Zend_Registry::get('aclRole');
        return $acl->isAllowed($aclRole, $resource, $previlege);
    }

    /**
     * Return link inside library
     *
     * @param string $action Controller action name
     * @param string $author override author name
     * @param string $title override title name
     * @param array $extraparams url extra params
     *
     * @return string
     */
    public function libraryUrl($action = null, $author = null, $title = null, $extraparams = null)
    {
        $params = array();

        if ($action !== null) {
            $params['action'] = $action;
        }

        if ($author === null) {
            if ($this->authorName != null) {
                $params['author'] = $this->authorName;
            }
        } else if ($author !== false) {
            $params['author'] = $author;
        }

        if ($title === null) {
            if ($this->titleName != null) {
                $params['title'] = $this->titleName;
            }
        } else if ($title !== false) {
            $params['title'] = $title;
        }

        if ($extraparams !== null) {
            $params['extraparams'] = $extraparams;
        }
        return $this->url($params, 'library');
    }

    /**
     * List of mail threads
     *
     * @param array $threads array of App_Mail_Thread
     *
     * @return string
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
                        . '<td>' . $this->profileLink($user) . '</td>'
                        . '<td>'
                        .     '<a href="'. $this->url(array('param' => $thread->getId())) . '#new-message">'
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

    /**
     * Helper for writing navigation links in left panel
     *
     * @param string $resource
     * @param string $name
     * @param string $url
     *
     * @return string
     */
    public function navigationLink($resource, $name, $url)
    {
        if ($resource) {
            $acl = Zend_Registry::get('acl');
            $aclRole = Zend_Registry::get('aclRole');
            if (!$acl->isAllowed($aclRole, $resource, 'view')) {
                return '';
            }
        }
        return '<a class="navigation-link" href="' . $url . '">'
            . $name
            . '</a>';
    }

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

        return '<a href="' . $this->url($params, 'user') . '" title="Посмотреть профиль ' . $login . '">'
            . $login
            . '</a>';
    }

    /**
     * Helper for writing book title with authors
     *
     * @var App_Library_Title $title
     *
     * @return string
     */
    public function titleLink(App_Library_Title $title)
    {
        $authors = $title->getAuthorsIndex();

        $firstAuthorName = '';
        $authorLinks = array();
        foreach($authors as $authorName) {
            if ($firstAuthorName === '') {
                $firstAuthorName = $authorName;
            }
            // TODO Link generation is very slow
            $authorLinks[] = '<a href="' . $this->libraryUrl(null, $authorName) . '">'
                . $authorName .'</a>';
        }

        return implode(', ', $authorLinks) . ' - '
            . '<a href="' . $this->libraryUrl(null, $firstAuthorName, $title->getName()) . '">'
            . $title->getName() . '</a>';
    }

    /**
     * Returns mark selection html-string
     *
     * @param App_Library_Title $title
     *
     * @return string
     */
    public function titleMark(App_Library_Title $title)
    {
        $titleMark = $title->getMark();
        if ($titleMark === null) {
            return '';
        }

        $result = '<p id="title-mark-' . $title->getId() . '"class="title-mark">Оценка: ';

        foreach (self::$_marks as $mark => $markName) {
            $result .= '<a href="#" class="mark' . $mark
                . ($titleMark !== false && $titleMark == $mark ? ' selected' : '')
                . '" title="' . $markName . '">&nbsp;'
                . $mark . '&nbsp;</a>';
        }

        $result .= '<a href="#" class="markremove" title="Удалить оценку">&nbsp;x&nbsp;</a></p>';

        return $result;
    }

    /**
     * List of users
     *
     * @param array $users array of App_User
     * @param int $listType
     * @param string $emptyMessage
     *
     * @return string
     */
    public function usersList(array $users = array(), $listType = self::LIST_DEFAULT,
        $emptyMessage = 'Нет друзей')
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
                    .  $this->profileLink($user) . '</td><td><ul>';


            if ($listType !== self::LIST_SENT_REQUESTS && $listType !== self::LIST_RECEIVED_REQUESTS
                && $userFriendState !== null && $userFriendState !== App_User_Friends::STATE_REQUEST_RECEIVED
                && $userFriendState !== App_User_Friends::STATE_REQUEST_SENT) {

                $result .= '<li><a href="'
                        . $this->url(array(
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
                        . $this->url(array(
                              'action' => 'accept',
                              'user' => $user->getLogin()
                          ), 'friends')
                        . '">Подтвердить</a></li>'
                        . '<li><a href="'
                        . $this->url(array(
                              'action' => 'decline',
                              'user' => $user->getLogin()
                          ), 'friends')
                        . '">Отменить</a></li>';
            }
            if ($listType === self::LIST_SENT_REQUESTS) {
                $result .= '<li><a href="'
                        . $this->url(array(
                              'action' => 'cancel',
                              'user' => $user->getLogin()
                          ), 'friends')
                        . '">Отменить</a></li>';
            }
            $result .= '<li><a href="'
                    . $this->url(array(
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
