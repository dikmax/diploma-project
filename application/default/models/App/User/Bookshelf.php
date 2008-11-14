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
 * App_User_Bookshelf description
 */
class App_User_Bookshelf
{
    /**
     * Connected user
     *
     * @var App_User
     */
    protected $_user;

    /**
     * Constructs user bookshelf object
     *
     * @param array $construct
     * Available indices
     * <ul>
     *   <li><code>user</code>: connected user (<b>App_User</b>)</li>
     * </ul>
     */
    public function __construct($construct)
    {
        if (isset($construct['user'])) {
            if ($construct['user'] instanceof App_User) {
                $this->_user = $construct['user'];
            } else {
                throw new App_User_Bookshelf_Exception('user index must be instance of App_User');
            }
        } else {
            throw new App_User_Bookshelf_Exception('user index is obligatory');
        }
    }

    public function getTitles()
    {
        $db = Zend_Registry::get('db');

        // TODO Delete some fields
        $titles = $db->fetchAll('SELECT t.lib_title_id, t.name, '
                .     't.authors_index, t.description_text_id, '
                .     't.front_description, t.lib_writeboard_id '
                . 'FROM lib_user_bookshelf s '
                . 'LEFT JOIN lib_title t USING (lib_title_id) '
                . 'WHERE s.lib_user_id = :lib_user_id',
                array(':lib_user_id' => $this->_user->getId()));

        $result = array();
        foreach ($titles as $title) {
            $result[] = new App_Library_Title($title);
        }
        return $result;
    }
}