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
 * App_User_Bookshelf description
 */
class App_User_Bookshelf implements App_Tag_Cloud_Reader_Interface
{
    /**
     * Connected user
     *
     * @var App_User
     */
    protected $_user;

    /**
     * Titles in bookshelf
     *
     * @var array
     */
    protected $_titles;

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

        $this->_titles = null;
    }

    /**
     * Returns title objects
     */
    public function getTitles()
    {
        if ($this->_titles === null) {
            $db = Zend_Registry::get('db');

            // TODO Delete some fields
            $titles = $db->fetchAll('SELECT t.lib_title_id, t.name, '
                    .     't.authors_index, t.description_text_id, '
                    .     't.front_description, t.lib_writeboard_id '
                    . 'FROM lib_user_bookshelf s '
                    . 'LEFT JOIN lib_title t USING (lib_title_id) '
                    . 'WHERE s.lib_user_id = :lib_user_id',
                    array(':lib_user_id' => $this->_user->getId()));

            $this->_titles = array();
            foreach ($titles as $title) {
                $this->_titles[] = new App_Library_Title($title);
            }
        }
        return $this->_titles;
    }

    /**
     * Adds new title to bookshelf
     *
     * @param App_Library_Title $title
     */
    public function addTitle(App_Library_Title $title)
    {
        $db = Zend_Registry::get('db');

        $data = array(
            'lib_user_id' => $this->_user->getId(),
            'lib_title_id' => $title->getId()
        );
        $db->insert('lib_user_bookshelf', $data);

        if ($this->_titles !== null) {
            $this->_titles[] = $title;
        }
    }

    /**
     * @see App_Tag_Clour_Reader_Interface::readTagCloudList()
     *
     * @return App_Tag_Cloud_Collection
     */
    public function readTagCloudList()
    {
        $db = Zend_Registry::get('db');
        $tags = $db->fetchAll('SELECT lib_tag_id, count(*) count, lib_tag.name '
            . 'FROM lib_title_has_tag '
            . 'LEFT JOIN lib_tag USING(lib_tag_id) '
            . 'WHERE lib_user_id = :lib_user_id '
            . 'GROUP BY lib_tag_id',
            array(':lib_user_id' => $this->_user->getId()));

        $result = new App_Tag_Cloud_Collection();
        foreach ($tags as $tag) {
            $result[$tag['lib_tag_id']] = new App_Tag_Data($tag['lib_tag_id'], $tag['name'], $tag['count']);
        }

        return $result;
    }
}