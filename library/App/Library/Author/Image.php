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
 * Author image model
 */
class App_Library_Author_Image
{
    /**
     * Index for database table <code>lib_author_image_id</code>
     *
     * @var int
     */
    protected $_libAuthorImageId;
    
    /**
     * Image author (who's painted)
     *
     * @var App_Library_Author
     */
    protected $_author;
    
    /**
     * Path to image
     *
     * @var string
     */
    protected $_path;
    
    /**
     * Image adding date
     *
     * @var App_Date
     */
    protected $_imageDate;
    
    /**
     * Constructs image object
     * @param array $construct
     * Available indices:
     * <ul>
     *   <li><code>lib_author_image_id</code>: database id (<b>int</b>)</li>
     *   <li><code>id</code>: alias for <code>lib_author_image_id</code> (<b>int</b>)</li>
     *   <li><code>author</code>: author, which pictured on image (<b>App_Library_Author</b>)</li>
     *   <li><code>path</code>: path to image (<b>string</b>)</li>
     *   <li><code>image_date</code>: image add date (<b>int|string|array|App_Date</b>)</li>
     * </ul>
     */
    public function __construct($construct)
    {
        // Id
        if (isset($construct['lib_author_image_id'])) {
            $this->_libAuthorImageId = $construct['lib_author_image_id'];
        } else if (isset($construct['id'])) {
            $this->_libAuthorImageId = $construct['id'];
        }
        
        // Author
        if (!isset($construct['author'])) {
            throw new App_Library_Author_Image_Exception('author index is required');
        }
        if (!($construct['author'] instanceof App_Library_Author)) {
            throw new App_Library_Author_Image_Exception('author index must be '
                . 'instance of App_Library_Author');
        }
        $this->_author = $construct['author'];
        
        // Path
        if (!isset($construct['path']) || !is_string($construct['path'])) {
            throw new App_Library_Author_Image_Exception('path index is required '
                . 'and must be of a string type');
        }
        $this->_path = $construct['path'];
        
        // Image date
        $this->_imageDate = isset($construct['image_date'])
            ? new App_Date($construct['image_date'])
            : App_Date::now();
    }
    
    /*
     * Setters and getters
     */
    
    /**
     * Returns database id.
     *
     * @return int
     */
    public function getLibAuthorImageId()
    {
        return $this->_libAuthorImageId;
    }
    
    /**
     * Returns database id (alias for <code>getLibAuthorImageId</code>).
     *
     * @return int
     */
    public function getId()
    {
        return $this->_libAuthorImageId;
    }
    
    /**
     * Returns App_Library_Author
     *
     * @return App_Library_Author
     */
    public function getAuthor()
    {
        return $this->_author;
    }
    
    /**
     * Returns image path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->_path;
    }
    
    /**
     * Returns image add date
     *
     * @return App_Date
     */
    public function getImageDate()
    {
        return $this->_imageDate;
    }
}