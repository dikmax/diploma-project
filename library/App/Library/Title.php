<?php
/**
 * Books social network
 *
 * LICENSE: Closed source
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id$
 */

require_once 'App/Library/Author.php';
require_once 'App/Text.php';
require_once 'App/Writeboard.php';

/**
 * Library Title model
 */
class App_Library_Title
{
    /**
     * Index for database table <code>lib_title</code>
     *
     * @var int
     */
    protected $_libTitleId;

    /**
     * Title string
     *
     * @var string
     */
    protected $_name;

    /**
     * Index of authors in format "author1#author2"
     *
     * @var string
     */
    protected $_authorsIndex;

    /**
     * Title authors
     *
     * @var array
     */
    protected $_authors;

    /**
     * Array of author indices
     *
     * @var array
     */
    protected $_authorsIndexArray;

    /**
     * Description shown on front page
     *
     * @var string
     */
    protected $_frontDescription;

    /**
     * Title's writeboard id
     *
     * @var int
     */
    protected $_writeboardId;

    /**
     * Title's writeboard
     *
     * @var App_Writeboard
     */
    protected $_writeboard;

    /**
     * ID of description text
     *
     * @var int
     */
    protected $_descriptionId;

    /**
     * Description text
     *
     * @var App_Text
     */
    protected $_description;

    /**
     * Mark for temporary use
     *
     * @var int
     */
    protected $_mark;

    /**
     * Similar titles
     *
     * @var array of App_Library_Title
     */
    protected $_similarTitles;

    /**
     * Constructs title object
     *
     * @param array $construct
     * Available indices:
     * <ul>
     *   <li><code>lib_title_id</code>: database id (<b>int</b>)</li>
     *   <li><code>id</code>: alias for <code>lib_title_id</code> (<b>int</b>)</li>
     *   <li><code>name</code>: title string (<b>string</b>)</li>
     *   <li><code>authors</code>: authors (<b>array</b>)</li>
     *   <li><code>authors_index</code>: index of authors (<b>string</b>)</li>
     *   <li><code>description_text_id</code>: id of description text (<b>int</b>)</li>
     *   <li><code>front_description</code>: description on front page (<b>string</b>)</li>
     *   <li><code>lib_writeboard_id</code>: writeboard id (<b>int</b>)</li>
     *   <li><code>mark</code>: mark (<b>int</b>)</li>
     * </ul>
     */
    public function __construct($construct)
    {
        // Id
        if (isset($construct['lib_title_id'])) {
            $this->_libTitleId = $construct['lib_title_id'];
        } else if (isset($construct['id'])) {
            $this->_libTitleId = $construct['id'];
        } else {
            $this->_libTitleId = null;
        }

        // Name
        $this->_name = isset($construct['name'])
            ? $construct['name']
            : '';

        // Authors
        $this->_authors = isset($construct['authors'])
            ? $construct['authors']
            : null;

        // Authors index
        if (isset($construct['authors_index'])) {
            $this->_authorsIndex = $construct['authors_index'];
        } else if ($this->_authors !== null) {
            $this->_authorsIndex = '';
            foreach ($this->_authors as $author) {
                if ($this->_authorsIndex !== '') {
                    $this->_authorsIndex .= '#';
                }
                $this->_authorsIndex .= $author->getName();
            }
        } else {
            $this->_authorsIndex = '';
        }

        $this->_authorsIndexArray = null;

        // Description
        $this->_descriptionId = isset($construct['description_text_id'])
            ? $construct['description_text_id']
            : null;

        $this->_description = null;

        // Front description
        $this->_frontDescription = isset($construct['front_description'])
            ? $construct['front_description']
            : '';

        // Writeboard
        $this->_writeboardId = isset($construct['lib_writeboard_id'])
            ? $construct['lib_writeboard_id']
            : null;

        $this->_writeboard = null;

        // Mark
        $this->_mark = isset($construct['mark'])
            ? $construct['mark']
            : null;

        // Similar titles
        $this->_similarTitles = null;
    }

    /**
     * Releases pointers for garbage collection
     */
    public function __destruct()
    {
        unset($this->_authors);
        unset($this->_writeboard);
        unset($this->_description);
    }

    /**
     * Writes/updates title into database
     */
    public function write()
    {
        $db = Zend_Registry::get('db');

        if ($this->_libTitleId === null) {
            // Create new title

            // Creating description
            if ($this->_descriptionId === null) {
                $description = new App_Text(array(
                    'text' => ''
                ));
                $description->write();
                $this->_descriptionId = $description->getId();
                $this->_description = $description;
            }

            // Creating writeboard
            if ($this->_writeboardId === null) {
                $writeboard = new App_Writeboard(array(
                    'owner_description' => 'New title'
                ));
                $writeboard->write();

                $this->_writeboardId = $writeboard->getId();
                $this->_writeboard = $writeboard;
            }

            $data = array(
                'name' => $this->_name,
                'authors_index' => $this->_authorsIndex,
                'description_text_id' => $this->_descriptionId,
                'front_description' => '',
                'lib_writeboard_id' => $this->_writeboardId
            );
            $db->insert('lib_title', $data);

            // Updating writoboard
            $this->_libTitleId = $db->lastInsertId();
            $writeboard->setOwnerDescription('Title ' . $this->_libTitleId);
            $writeboard->write();

            // Creating link with authors
            if ($this->_authors !== null) {
                foreach ($this->_authors as $author) {
                    $data = array(
                        'lib_author_id' => $author->getId(),
                        'lib_title_id' => $this->_libTitleId
                    );
                    $db->insert('lib_author_has_title', $data);
                }
            }
        } else {
            $data = array(
                'name' => $this->_name,
                'front_description' => $this->_frontDescription
            );
            $db->update('lib_title', $data,
                $db->quoteInto('lib_title_id = ?', $this->_libTitleId));
        }
    }

    /**
     * Returns title by name
     *
     * @param string|App_Library_Author $authorName author name
     * @param string $titleName title
     *
     * @return App_Library_Title
     */
    public static function getByName($authorName, $titleName)
    {
        if (!is_string($titleName)) {
            require_once 'App/Library/Exception.php';
            throw new App_Library_Exception('Title url must be string');
        }

        if ($authorName instanceof App_Library_Author) {
            $author = $authorName;
        } else {
            $author = App_Library_Author::getByName($authorName);
        }
        $authorId = $author->getId();

        require_once 'App/Db/Table/Title.php';
        $table = new App_Db_Table_Title();
        $row = $table->getTitleByName($authorId, $titleName);

        if ($row === false) {
            return null;
        }

        return new self($row);
    }

    /**
     * Returns title by id
     *
     * @param int $titleId
     *
     * @return App_Library_Title
     */
    public static function getById($titleId)
    {
        if (!is_numeric($titleId)) {
            require_once 'App/Library/Exception.php';
            throw new App_Library_Exception('Id must be a number');
        }

        require_once 'App/Db/Table/Title.php';
        $table = new App_Db_Table_Title();
        $row = $table->find($titleId);
        if ($row === false) {
            return null;
        }

        return new self($row[0]->toArray());
    }

    /**
     * Update list of similar titles
     */
    public function updateSimilar()
    {
        require_once 'App/Db/Table/TitleSimilar.php';
        $similarTable = new App_Db_Table_TitleSimilar();
        $similarTable->updateSimilar($this->_libTitleId);
    }

    /*
     * Setters and getters
     */

    /**
     * Returns database id
     *
     * @return int
     */
    public function getLibTitleId()
    {
        return $this->_libTitleId;
    }

    /**
     * Returns database id (alias for <code>getLibTitleId</code>)
     *
     * @return int
     */
    public function getId()
    {
        return $this->_libTitleId;
    }

    /**
     * Returns title
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Returns id of description text
     *
     * @return int
     */
    public function getDescriptionId()
    {
        return $this->_descriptionId;
    }

    /**
     * Returns description text object
     *
     * @return App_Text
     */
    public function getDescription()
    {
        if ($this->_description === null) {
            $this->_description = App_Text::read($this->_descriptionId);
        }
        return $this->_description;
    }

    /**
     * Returns description text. Shorthand for <code>getDescription()->getText()</code>
     *
     * @return string
     */
    public function getText()
    {
        return $this->getDescription()->getText();
    }

    /**
     * Sets new text and updates front description
     *
     * @param string $text New text
     * @param boolean $noWrite <code>true<code> if don't update database
     */
    public function setText($text, $noWrite = false)
    {
        $this->getDescription(); // Ensure that $this->_description initialized
        $this->_description->setText($text);

        // TODO write front description transform
        $this->_frontDescription = $text;
        if (!$noWrite) {
            $this->write();
        }
    }

    /**
     * Returns description shown on main page
     *
     * @return string
     */
    public function getFrontDescription()
    {
        return $this->_frontDescription;
    }

    /**
     * Returns title writeboard id
     *
     * @return int
     */
    public function getWriteboardId()
    {
        return $this->_writeboardId;
    }

    /**
     * Returns title writeboard
     *
     * @return App_Writeboard
     */
    public function getWriteboard()
    {
        if ($this->_writeboard === null) {
            if ($this->_writeboardId === null) {
                $this->_writeboard = false;
            } else {
                $this->_writeboard = new App_Writeboard(array(
                    'lib_writeboard_id' => $this->_writeboardId
                ));
            }
        }
        return $this->_writeboard;
    }

    /**
     * Returns array of authors
     *
     * @return array
     */
    public function getAuthors()
    {
        if ($this->_authors === null) {
            $this->_authors = array();
            // TODO write reading list of authors
        }
        return $this->_authors;
    }

    /**
     * Returns author index array ('url' => 'name')
     *
     * @return array
     */
    public function getAuthorsIndex()
    {
        if ($this->_authorsIndexArray === null) {
            $this->_authorsIndexArray = array();

            $parts = explode('#', $this->_authorsIndex);

            for($i = 0; $i < count($parts); ++$i) {
                if ($parts[$i] != '') {
                    $this->_authorsIndexArray[] = $parts[$i];
                }
            }
        }
        return $this->_authorsIndexArray;
    }

    /**
     * Returns mark
     *
     * @return int
     */
    public function getMark()
    {
        return $this->_mark;
    }

    /**
     * Sets mark
     *
     * @param int $_mark
     */
    public function setMark($mark)
    {
        $this->_mark = $mark;
    }

    /**
     * Returns similar titles
     *
     * @return array of App_Library_Titles
     */
    public function getSimilarTitles()
    {
        if ($this->_similarTitles === null) {
            require_once 'App/Db/Table/TitleSimilar.php';
            $table = new App_Db_Table_TitleSimilar();
            $titles = $table->getTitles($this->_libTitleId);

            $this->_similarTitles = array();
            foreach ($titles as $row) {
                $this->_similarTitles[] = new App_Library_Title($row);
            }
        }

        return $this->_similarTitles;
    }
}
