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
class App_User_Bookshelf extends App_Acl_Resource_Abstract implements App_Tag_Cloud_Reader_Interface
{
    /**
     * -2 mark
     */
    const RELATION_MARK_2 = 1;
    /**
     * -1 mark
     */
    const RELATION_MARK_1 = 2;
    /**
     * 0 mark
     */
    const RELATION_MARK0 = 3;
    /**
     * 1 mark
     */
    const RELATION_MARK1 = 4;
    /**
     * 2 mark
     */
    const RELATION_MARK2 = 5;
    /**
     * Suggested book
     */
    const RELATION_SUGGESTED_BOOK = 6;

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

        if ($this->_user->getId() === null) {
            throw new App_User_Bookshelf_Exception('User id isn\'t set');
        }

        $this->_titles = null;

        $this->registerResource();
    }

    public function __destruct()
    {
        $this->unregisterResource();
        $this->_user = null;
        unset($this->_titles);
    }


    /**
     * Returns title objects
     */
    public function getTitles()
    {
        if ($this->_titles === null) {
            $table = new App_Db_Table_UserBookshelf();

            $titles = $table->findTitlesByUserId($this->_user->getId());

            $this->_titles = array();
            foreach ($titles as $title) {
                $this->_titles[] = new App_Library_Title($title);
            }
        }
        return $this->_titles;
    }

    /**
     * Returns all marks for user
     *
     * @param boolean $markCast convert mark from range 1 to 5 to range -2 to 2
     *
     * @return array $titleId => $mark
     */
    public function getTitlesMarks($markCast = true)
    {
        $table = new App_Db_Table_UserBookshelf();

        return $table->getMarks($this->_user->getId(), $markCast);
    }

    /**
     * Adds new title to bookshelf
     *
     * @param App_Library_Title $title
     */
    public function addTitle(App_Library_Title $title)
    {
        $table = new App_Db_Table_UserBookshelf();
        $table->insert(array(
            'lib_user_id' => $this->_user->getId(),
            'lib_title_id' => $title->getId()
        ));

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

    /**
     * Gets title mark from database
     *
     * @param App_Library_Title|int $title
     *
     * @return App_Library_Title|int
     */
    public function getMark($title)
    {
        if ($title instanceof App_Library_Title) {
            $titleId = $title->getId();
        } else if (is_numeric($title)) {
            $titleId = (int)$title;
        } else {
            throw new App_User_Bookshelf_Exception('$title mustbe instance of App_Library_Title or int');
        }

        $table = new App_Db_Table_UserBookshelf();
        $mark = $table->getMark($this->_user->getId(), $titleId);

        if ($title instanceof App_Library_Title) {
            $title->setMark($mark);
            return $title;
        } else {
            return $mark;
        }
    }

    /**
     * Sets mark for title
     *
     * @param App_Library_Title|int $title
     * @param int $mark
     */
    public function setMark($title, $mark)
    {
        if ($title instanceof App_Library_Title) {
            $titleId = $title->getId();
        } else if (is_numeric($title)) {
            $titleId = (int)$title;
        } else {
            throw new App_User_Bookshelf_Exception('$title mustbe instance of App_Library_Title or int');
        }

        $table = new App_Db_Table_UserBookshelf();
        $table->setMark($this->_user->getId(), $titleId, $mark);
    }

    /**
     * Removes mark for title
     *
     * @param App_Library_Title|int $title
     */
    public function removeMark($title)
    {
        if ($title instanceof App_Library_Title) {
            $titleId = $title->getId();
        } else if (is_numeric($title)) {
            $titleId = (int)$title;
        } else {
            throw new App_User_Bookshelf_Exception('$title mustbe instance of App_Library_Title or int');
        }

        $table = new App_Db_Table_UserBookshelf();
        $table->removeMark($this->_user->getId(), $titleId);
    }

    /**
     * Update suggested books
     */
    public function updateSuggestedBooks()
    {
        $table = new App_Db_Table_UserBookshelf();
        $table->updateSuggestedBooks($this->_user->getId());
    }

    /**
     * @see App_Acl_Resource_Abstract::getResourceParentId()
     *
     * @return string
     */
    protected function getResourceParentId()
    {
        return 'bookshelf';
    }

    /**
     * @see Zend_Acl_Resource_Interface::getResourceId()
     *
     * @return string
     */
    public function getResourceId()
    {
        return 'bookshelf-' . $this->_user->getId();
    }
}