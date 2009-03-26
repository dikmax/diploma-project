<?php
/**
 * Books social network
 *
 * LICENSE: Closed source
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id:$
 */

require_once 'App/Library/Author.php';
require_once 'App/Date.php';

/**
 * Author images container
 */
class App_Library_Author_Images // TODO impelement array interfaces
{
    /**
     * Image author (who's painted)
     *
     * @var App_Library_Author
     */
    protected $_author;
	
    /**
     * Array of images
     * 
     * @var array
     */
    protected $_images;

    /**
     * Front image
     * 
     * @var App_Library_Author_Image
     */
    protected $_frontImage;
    
    /**
     * Working db table
     * 
     * @var App_Db_Table_AuthorImage
     */
    protected $_table;
    
	/**
	 * Constructs author images container
     * @param array $construct
     * Available indices:
     * <ul>
     *   <li><code>author</code>: author, which pictured on image (<b>App_Library_Author</b>)</li>
     * </ul>
	 */
	public function __construct($construct)
	{
        // Author
        if (!isset($construct['author'])) {
            throw new App_Library_Author_Exception('author index is required');
        }
        if (!($construct['author'] instanceof App_Library_Author)) {
            throw new App_Library_Author_('author index must be '
                . 'instance of App_Library_Author');
        }
        $this->_author = $construct['author'];
        
        $this->_table = new App_Db_Table_AuthorImage();
        
        // Lazy init stuff
        $this->_images = null;
        
        $this->_frontImage = null;
	}
	
	public function getImages()
	{
		if ($this->_images === null) {
			$res = $this->_table->findImagesByAuthor($this->_author->getLibAuthorId());
			$this->_images = array();
			foreach ($res as $row) {
				$row['author'] = $this->_author;
				$this->_images[] = new App_Library_Author_Image($row);
			} 
		}
		
		return $this->_images;
	}
	
	/**
	 * Returns front image
	 * 
	 * @return App_Library_Author_Image
	 */
	public function getFrontImage()
	{
		if ($this->_frontImage === null) {
			$row = $this->_table->getFrontImage($this->_author->getLibAuthorId());
			$row['author'] = $this->_author;
			$this->_frontImage = new App_Library_Author_Image($row);
		}
		
		return $this->_frontImage;
	}
	
	/**
	 * Add new image to database
	 * 
	 * @param string $path
	 * @return App_Library_Author_Image
	 */
	public function addImage($path) {
		$image = new App_Library_Author_Image(array(
			'author' => $this->_author,
			'path' => $path
		));
		$image->write();
		
		if ($this->_images !== null) {
			$this->_images[] = $image;
		}
		
		return $image;
	}
}