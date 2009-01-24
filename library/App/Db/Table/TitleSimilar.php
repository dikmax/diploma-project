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
 * Similar titles table model
 *
 * @author dikmax
 * @version
 */
class App_Db_Table_TitleSimilar extends App_Db_Table_Abstract
{
    /**
     * The default table name
     */
    protected $_name = 'lib_title_similar';

    /**
     * Primery key
     */
    protected $_primary = array(
        'title1_id',
        'title2_id');

    /**
     * This table doesn't supports auto-incremental key
     */
    protected $_sequence = false;

    /**
     * Foreign keys
     */
    protected $_referenceMap = array(
        'Title' => array(
            'columns'           => 'title1_id',
            'refTableClass'     => 'App_Db_Table_Title',
            'refColumns'        => 'lib_title_id'
        ),
        'Similar' => array(
            'columns'           => 'title2_id',
            'refTableClass'     => 'App_Db_Table_Title',
            'refColumns'        => 'lib_title_id'
        )
    );

    /**
     * Updates titles similar to specified
     *
     * @param int $titleId
     */
    public function updateSimilar($titleId)
    {
        if (!is_numeric($titleId)) {
            throw new App_Exception('titleId is not numeric');
        }
        $table = new App_Db_Table_UserBookshelf();
        $titles = $table->getSimilarTitles($titleId);

        $this->delete($this->_db->quoteInto('title1_id = ?', $titleId));

        if (is_array($titles)) {
            foreach ($titles as $title) {
                $this->insert(array(
                    'title1_id' => $titleId,
                    'title2_id' => $title['lib_title_id'],
                    'avg' => $title['avg'],
                    'count' => $title['count']
                ));
            }
        }
    }
}
