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
     * Constructs title object
     * 
     * @param array $construct
     * Available indices:
     * <ul>
     *   <li><code>lib_title_id</code>: database id (<b>int</b>)</li>
     *   <li><code>id</code>: alias for <code>lib_title_id</code> (<b>int</b>)</li>
     *   <li><code>name</code>: title string (<b>string</b>)</li>
     *   <li><code>url</code>: title part of url (<b?string</b>)</li>
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
}
