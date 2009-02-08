v<?php
/**
 * Books social network
 *
 * LICENSE: Closed source
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id$
 */

require_once 'App/Db/Table/Abstract.php';

/**
 * Similar authors table model
 *
 * @author dikmax
 * @version
 */
class App_Db_Table_AuthorSimilar extends App_Db_Table_Abstract
{
    /**
     * The default table name
     */
    protected $_name = 'lib_author_similar';

    /**
     * Primery key
     */
    protected $_primary = array(
        'author1_id',
        'author2_id');

    /**
     * This table doesn't supports auto-incremental key
     */
    protected $_sequence = false;

    /**
     * Foreign keys
     */
    protected $_referenceMap = array(
        'Author' => array(
            'columns'           => 'author1_id',
            'refTableClass'     => 'App_Db_Table_Author',
            'refColumns'        => 'lib_author_id'
        ),
        'Similar' => array(
            'columns'           => 'author2_id',
            'refTableClass'     => 'App_Db_Table_Author',
            'refColumns'        => 'lib_author_id'
        )
    );

    /**
     * Updates authors similar to specified
     *
     * @param int $aurhorId
     */
    public function updateSimilar($authorId)
    {
        if (!is_numeric($authorId)) {
            throw new App_Exception('authorId must be numeric');
        }
        require_once 'App/Db/Table/UserBookshelf.php';
        $table = new App_Db_Table_UserBookshelf();
        $authors = $table->getSimilarAuthors($authorId);

        $this->delete($this->_db->quoteInto('author1_id = ?', $authorId));

        if (is_array($authors)) {
            foreach ($authors as $author) {
                $this->insert(array(
                    'title1_id' => $authorId,
                    'title2_id' => $author['lib_author_id'],
                    'avg' => $author['avg'],
                    'count' => $author['count']
                ));
            }
        }
    }
}
