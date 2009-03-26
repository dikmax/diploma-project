<?php
/**
 * Books social network
 *
 * LICENSE: Closed source
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id$
 */

require_once 'App/Library/Author/Image.php';
require_once 'App/Text.php';
require_once 'App/Writeboard.php';

/**
 * Library Author model
 */
class App_Library_Author
{
    /**
     * Instances counter for detecting memory leaks
     *
     * @var int
     */
    public static $instancesCount = 0;

    /**
     * Index for database table <code>lib_author</code>
     *
     * @var int
     */
    protected $_libAuthorId;

    /**
     * Author name
     *
     * @var string
     */
    protected $_name;

    /**
     * Description to show on front page
     *
     * @var string
     */
    protected $_frontDescription;

    /**
     * Author's writeboard id
     *
     * @var int
     */
    protected $_writeboardId;

    /**
     * Author's writeboard
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
     * Image shown of front page
     *
     * @var App_Library_Author_Image
     */
    protected $_frontImage;

    /**
     * Author images
     * 
     * @var App_Library_Author_Images
     */
    protected $_images;
    
    /**
     * Titles written by author
     *
     * @var array
     */
    protected $_titles;

    /**
     * Constructs author object
     *
     * @param array $construct
     * Available indices:
     * <ul>
     *   <li><code>lib_author_id</code>: database id (<b>int</b>)</li>
     *   <li><code>id</code>: alias for <code>lib_author_id</code> (<b>int</b>)</li>
     *   <li><code>name</code>: author name (<b>string</b>)</li>
     *   <li><code>description_text_id</code>: id of description text (<b>int</b>)</li>
     *   <li><code>front_description</code>: text to show on author's page (<b>string</b>)</li>
     *   <li><code>lib_writeboard_id</code>: writeboard id (<b>int</b>)</li>
     * </ul>
     */
    public function __construct($construct)
    {
        // Id
        if (isset($construct['lib_author_id'])) {
            $this->_libAuthorId = $construct['lib_author_id'];
        } elseif (isset($construct['id'])) {
            $this->_libAuthorId = $construct['id'];
        } else {
            $this->_libAuthorId = null;
        }

        // Name
        $this->_name = isset($construct['name'])
            ? $construct['name']
            : '';

        // Description
        $this->_descriptionId = isset($construct['description_text_id'])
            ? $construct['description_text_id']
            : null;

        $this->_description = null;

        // Front Description
        $this->_frontDescription = isset($construct['front_description'])
            ? $construct['front_description']
            : '';

        // Writeboard
        $this->_writeboardId = isset($construct['lib_writeboard_id'])
            ? $construct['lib_writeboard_id']
            : null;

        $this->_writeboard = null;

        // Lazy init stuff
        $this->_frontImage = null;

        $this->_images = null;
        
        $this->_titles = null;

        self::$instancesCount++;
    }

    /**
     * Releases pointers for garbage collection
     */
    public function __destruct()
    {
        unset($this->_writeboard);
        unset($this->_description);
        unset($this->_frontImage);

        self::$instancesCount--;
    }

    /**
     * Writes/updates author into database
     */
    public function write()
    {
        $db = Zend_Registry::get('db');

        if ($this->_libAuthorId === null) {
            // Create new author
            if ($this->_descriptionId === null) {
                $description = new App_Text(array(
                    'text' => ''
                ));
                $description->write();
                $this->_descriptionId = $description->getId();
                $this->_description = $description;
            }

            if ($this->_writeboardId === null) {
                $writeboard = new App_Writeboard(array(
                    'owner_description' => 'New author'
                ));
                $writeboard->write();

                $this->_writeboardId = $writeboard->getId();
                $this->_writeboard = $writeboard;
            }

            $data = array(
                'name' => $this->_name,
                'description_text_id' => $this->_descriptionId,
                'front_description' => '',
                'lib_writeboard_id' => $this->_writeboardId
            );
            $db->insert('lib_author', $data);

            $this->_libAuthorId = $db->lastInsertId();
            $writeboard->setOwnerDescription('Author ' . $this->_libAuthorId);
            $writeboard->write();

            // Adding default name
            $data = array(
                'lib_author_id' => $this->_libAuthorId,
                'name' => $this->_name
            );
            $db->insert('lib_author_name', $data);
        } else {
            // Update author
            $data = array(
                'name' => $this->_name,
                'front_description' => $this->_frontDescription
            );
            $db->update('lib_author', $data,
                $db->quoteInto('lib_author_id = ?', $this->_libAuthorId));
        }
    }

    /**
     * Returns author by name
     *
     * @param string $authorName Author name
     *
     * @return App_Library_Author
     *
     * @throws App_Library_Exception
     */
    public static function getByName($authorName)
    {
        if (!is_string($authorName)) {
            require_once 'App/Library/Exception.php';
            throw new App_Library_Exception('Author name must be a string');
        }

        require_once 'App/Db/Table/Author.php';
        $table = new App_Db_Table_Author();
        $row = $table->getAuthorByName($authorName);
        if ($row === false) {
            return null;
        }

        return new self($row);
    }

    /**
     * Returns author by id
     *
     * @param int $authorId
     *
     * @return App_Library_Author
     */
    public static function getById($authorId)
    {
        if (!is_numeric($authorId)) {
            require_once 'App/Library/Exception.php';
            throw new App_Library_Exception('Id must be a number');
        }

        require_once 'App/Db/Table/Author.php';
        $table = new App_Db_Table_Author();
        $row = $table->find($authorId);
        if ($row === false) {
            return null;
        }

        return new self($row[0]->toArray());
    }

    /**
     * Update list of similar authors
     */
    public function updateSimilar()
    {
        require_once 'App/Db/Table/AuthorSimilar.php';
        $similarTable = new App_Db_Table_AuthorSimilar();
        $similarTable->updateSimilar($this->_libAuthorId);
    }

    /*
     * Setters and getters
     */

    /**
     * Returns database id
     *
     * @return int
     */
    public function getLibAuthorId()
    {
        return $this->_libAuthorId;
    }

    /**
     * Returns database id (alias for <code>getLibAuthorId</code>)
     *
     * @return int
     */
    public function getId()
    {
        return $this->_libAuthorId;
    }

    /**
     * Returns author name
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
     * Returns text to show on author's main page
     *
     * @return string
     */
    public function getFrontDescription()
    {
        return $this->_frontDescription;
    }

    /**
     * Returns author's writeboard id
     *
     * @return int
     */
    public function getWriteboardId()
    {
        return $this->_writeboardId;
    }

    /**
     * Returns author's writeboard
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
     * Returns front image (if any)
     *
     * @return App_Library_Author_Image
     */
    public function getFrontImage()
    {
        if ($this->_frontImage === null) {
            if ($this->_libAuthorId === null) {
                require_once 'App/Library/Author/Exception.php';
                throw new App_Library_Author_Exception('_libAuthorId isn\'t defined');
            }
            $db = Zend_Registry::get('db');

            $row = $db->fetchRow('SELECT lib_author_image_id, path, image_date '
                 . 'FROM lib_author_image '
                 . 'WHERE lib_author_id = :lib_author_id',
                 array(':lib_author_id' => $this->_libAuthorId));

            if ($row === false) {
                $this->_frontImage = false;
            } else {
                $row['author'] = $this;
                $row['image_date'] = App_Date::fromMysqlString($row['image_date']);
                $this->_frontImage = new App_Library_Author_Image($row);
            }
        }
        return $this->_frontImage;
    }

    /**
     * Returns author images
     * 
     * @return App_Library_Author_Images
     */
    public function getImages()
    {
    	if ($this->_images === null) {
    		$this->_images = new App_Library_Author_Images(array(
    			'author' => $this
    		));
    	}
    	
    	return $this->_images;
    }
    
    /**
     * Returns all titles written by author
     *
     * @return array
     */
    public function getTitles()
    {
        if ($this->_titles === null) {
            $db = Zend_Registry::get('db');

            $titles = $db->fetchAll('SELECT  t.lib_title_id, t.name, '
                .     't.authors_index, t.description_text_id, t.front_description, '
                .     't.lib_writeboard_id '
                . 'FROM lib_author_has_title h '
                . 'LEFT JOIN lib_title t USING (lib_title_id) '
                . 'WHERE h.lib_author_id = :lib_author_id',
                array(':lib_author_id' => $this->_libAuthorId));

            $this->_titles = array();
            foreach ($titles as $row) {
                $this->_titles[] = new App_Library_Title($row);
            }
        }
        return $this->_titles;
    }
}