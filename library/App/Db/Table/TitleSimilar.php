<?php
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
            require_once 'App/Exception.php';
            throw new App_Exception('titleId is not numeric');
        }
        require_once 'App/Db/Table/UserBookshelf.php';
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

    /**
     * Returns similar titles
     *
     * @param int $titlesId
     *
     * @return array
     */
    public function getTitles($titleId)
    {
        $select = 'SELECT t.lib_title_id, t.name, '
            .     't.authors_index, t.description_text_id, t.front_description, '
            .     't.lib_writeboard_id '
            . 'FROM lib_title_similar s '
            . 'LEFT JOIN lib_title t ON t.lib_title_id = s.title2_id '
            . 'WHERE s.title1_id = :title_id';
        return $this->_db->fetchAll($select,
            array(':title_id' => $titleId));
    }
}
