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
     * Title url string
     * 
     * @var string
     */
    protected $_url;
    
    /**
     * Index of authors in format "author1#url1#author2#url2"
     * 
     * @var string
     */
    protected $_authorsIndex;
    
    /**
     * Array of author indices ('url' => 'name')
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
     * Constructs title object
     * 
     * @param array $construct
     * Available indices:
     * <ul>
     *   <li><code>lib_title_id</code>: database id (<b>int</b>)</li>
     *   <li><code>id</code>: alias for <code>lib_title_id</code> (<b>int</b>)</li>
     *   <li><code>name</code>: title string (<b>string</b>)</li>
     *   <li><code>url</code>: title part of url (<b>string</b>)</li>
     *   <li><code>authors_index</code>: index of authors (<b>string</b>)</li>
     *   <li><code>description_text_id</code>: id of description text (<b>int</b>)</li>
     *   <li><code>front_description</code>: description on front page (<b>string</b>)</li>
     *   <li><code>lib_writeboard_id</code>: writeboard id (<b>int</b>)</li>
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
        
        // Url
        $this->_url = isset($construct['url'])
            ? $construct['url']
            : '';

        // Authors index
        $this->_authorsIndex = isset($construct['authors_index'])
            ? $construct['authors_index']
            : '';
        
        $this->_authorsIndexArray = null;
            
        // Description
        $this->_descriptionId = isset($construct['description_text_id'])
            ? $construct['description_text_id']
            : '';
        
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
    }
    
    /**
     * Writes/updates title into database
     */
    public function write()
    {
        $db = Zend_Registry::get('db');
        
        if ($this->_libTitleId === null) {
            // TODO write create
        } else {
            $data = array(
                'name' => $this->_name,
                'url' => $this->_url,
                'front_description' => $this->_frontDescription
            );
            $db->update('lib_title', $data,
                $db->quoteInto('lib_title_id = ?', $this->_libTitleId));
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
     * Returns title part or url
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
     * Returns author index array ('url' => 'name')
     * 
     * @return array
     */
    public function getAuthorsIndex()
    {
        if ($this->_authorsIndexArray === null) {
            $this->_authorsIndexArray = array();
            
            $parts = explode('#', $this->_authorsIndex);
            
            for($i = 0; $i < count($parts); $i = $i + 2) {
                if (!isset($parts[$i + 1]) || $parts[$i + 1] == '') {
                    break;
                }
                $this->_authorsIndexArray[$parts[$i+1]] = $parts[$i];
            }
        }
        return $this->_authorsIndexArray;
    }
}
