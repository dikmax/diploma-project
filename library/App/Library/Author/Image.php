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
require_once 'App/Date.php';

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
     * Count of positive marks
     * @var int
     */
    protected $_positive;
    
    /**
     * Count of negative marks
     * @var int
     */
    protected $_negative;
    
    /**
     * Photo reting
     * 
     * @var double
     */
    protected $_rating;
    
    /**
     * Count of abuses
     * 
     * @var int
     */
    protected $_abuse;
    
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
     *   <li><code>positive</code>: count of positive marks (<b>int</b>)</li>
     *   <li><code>negative</code>: count of negative marks (<b>int</b>)</li>
     *   <li><code>rating</code>: rating of photo (<b>double</b>)</li>
     *   <li><code>abuse</code>: count of abuses (<b>int</b>)</li>
     * </ul>
     */
    public function __construct($construct)
    { 
    	// Id
        if (isset($construct['lib_author_image_id'])) {
            $this->_libAuthorImageId = $construct['lib_author_image_id'];
        } else if (isset($construct['id'])) {
            $this->_libAuthorImageId = $construct['id'];
        } else {
        	$this->_libAuthorImageId = null;
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
                . 'and must be of a string');
        }
        $this->_path = $construct['path'];

        // Image date
        $this->_imageDate = isset($construct['image_date'])
            ? new App_Date($construct['image_date'])
            : App_Date::now();
            
        // Positive
        $this->_positive = isset($construct['positive'])
        	? (int)$construct['positive']
        	: 0;
        	
        // Negative
        $this->_negative = isset($construct['negative'])
        	? (int)$construct['negative']
        	: 0;
        	
        // Rating
    	$this->_rating = isset($construct['rating'])
    		? (double)$construct('rating')
    		: 1.0;
    	
    	// Abuses
    	$this->_abuse = isset($construct['abuse'])
    		? (int)$construct['abuse']
    		: 0;
    }

    /**
     * Saves image to database
     */
    public function write()
    {
    	if ($this->_libAuthorImageId === null) {
    		// Create new
    		$table = new App_Db_Table_AuthorImage();
    		$insertId = $table->insert(array(
    			'lib_author_id' => $this->_author->getLibAuthorId(),
    			'path' => $this->_path,
    			'image_date' => $this->_imageDate->toMysqlString(),
    			'positive' => $this->_positive,
    			'negative' => $this->_negative,
    			'rating' => $this->_rating,
    			'abuse' => $this->_abuse
    		));
    		$this->_libAuthorImageId = $insertId;
    	} else {
    		// Update existing
    		$table = new App_Db_Table_AuthorImage();
    		$table->update(array(
    			'path' => $this->_path,
    			'image_date' => $this->_imageDate->toMysqlString(),
    			'positive' => $this->_positive,
    			'negative' => $this->_negative,
    			'rating' => $this->_rating,
    			'abuse' => $this->_abuse
    		), $table->getAdapter()->quoteInto('lib_author_image_id = ?', $this->_libAuthorImageId));
    	}
    }
    
    /**
     * Recalculates rating
     * 
     * @return double
     */
    public function updateRating()
    {
    	if ($this->_negative == 0) {
    		if ($this->_positive == 0) {
    			$this->_rating = 1.0;
    		} else {
    			$this->_rating = 1000000.0;
    		}
    	} else {
    		$this->_rating = (double)$this->_positive / (double)$this->_negative;
    	}
    	
    	return $this->_rating;
    }
    
    /**
     * Adds one positive mark
     * 
     * @return int
     */
    public function incPositive()
    {
    	++$this->_positive;
    	$this->updateRating();
    	return $this->_positive;
    }
    
    /**
     * Add one negative mark
     * 
     * @return int
     */
    public function incNegative()
    {
    	++$this->_negative;
    	$this->updateRating();
    	return $this->_negative;
    }
    
    /**
     * Add one abuse
     * 
     * @return int
     */
    public function incAbuse()
    {
    	++$this->_abuse;
    	return $this->_abuse;
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
     * Sets new image path
     * @param string $path
     */
    public function setPath($path)
    {
    	$this->_path = $path;
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
    
    /**
     * Sets image date
     * 
     * @param App_Date $date
     */
    public function setImageData(App_Date $date)
    {
    	$this->_imageDate;
    }
    
    /**
     * Returns count of positive marks
     * 
     * @return int
     */
    public function getPositive()
    {
    	return $this->_positive;
    }
    
    /**
     * Sets count of positive marks
     * 
     * @param int $positive
     */
    public function setPositive($positive)
    {
    	$this->_positive = $positive;
    	$this->updateRating();
    }
    
    /**
     * Returns count of negative marks
     * 
     * @return int
     */
    public function getNegative()
    {
    	return $this->_negative;
    }
    
    /**
     * Sets count of negative marks
     * 
     * @param int $negative
     */
    public function setNegative($negative)
    {
    	$this->_negative = $negative;
    	$this->updateRating();
    }
    
    /**
     * Returns photo raing
     * 
     * @return double
     */
    public function getRating()
    {
    	return $this->_rating;
    }
    
    /**
     * Returns abuses count
     * 
     * @return int
     */
    public function getAbuse()
    {
    	return $this->_abuse;
    }
    
    /**
     * Sets abuses count
     * 
     * @param $abuse
     */
    public function setAbuse($abuse)
    {
    	$this->_abuse = $abuse;
    }
}