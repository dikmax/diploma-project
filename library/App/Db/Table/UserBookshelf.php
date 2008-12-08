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
 * User bookshelf table model
 *
 * @author dikmax
 * @version
 */
class App_Db_Table_UserBookshelf extends Zend_Db_Table_Abstract
{
    /**
     * The default table name
     */
    protected $_name = 'lib_user_bookshelf';

    /**
     * Primery key
     */
    protected $_primary = 'lib_user_bookshelf_id';

    /**
     * This table supports auto-incremental key
     */
    protected $_sequence = true;

    /**
     * Foreign keys
     */
    protected $_referenceMap = array(
        'Title' => array(
            'columns'           => 'lib_title_id',
            'refTableClass'     => 'App_Db_Table_Title',
            'refColumns'        => 'lib_title_id'
        ),
        'User' => array(
            'columns'           => 'lib_user_id',
            'refTableClass'     => 'App_Db_Table_User',
            'refColumns'        => 'lib_user_id'
        )
    );

    /**
     * Returns titles by user id
     *
     * @param int $userId
     *
     */
    public function findTitlesByUserId($userId)
    {
        $select = $this->getAdapter()->select()
            ->from($this->_name, array())
            ->joinLeftUsing('lib_title', '`lib_title_id`')
            ->where('`lib_user_bookshelf`.`lib_user_id` = ?', $userId);

        return $this->getAdapter()->fetchAll($select);
    }
}
