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
 * Library Author model
 */
class App_Library_Author
{
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
     * Url name component
     *
     * @var string
     */
    protected $_url;
    
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
     *   <li><code>url</code>: url name component (<b>string</b>)</li>
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
        
        // Url
        $this->_url = isset($construct['url'])
            ? $construct['url']
            : '';

        // Description
        $this->_descriptionId = isset($construct['description_text_id'])
            ? $construct['description_text_id']
            : '';
        
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
        
        $this->_titles = null;
    }
    
    /**
     * Writes/updates author into database
     */
    public function write()
    {
        $db = Zend_Registry::get('db');
        
        if ($this->_libAuthorId === null) {
            // TODO write create
        } else {
            $data = array(
                'name' => $this->_name,
                'url' => $this->_url,
                'front_description' => $this->_frontDescription
            );
            $db->update('lib_author', $data,
                $db->quoteInto('lib_author_id = ?', $this->_libAuthorId));
        }
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
     * Returns name component of url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->_url;
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
     * Returns all titles written by author
     * 
     * @return array
     */
    public function getTitles()
    {
        // TODO write smth here
    }
}