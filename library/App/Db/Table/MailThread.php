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
 * Mail thread table model
 *
 * @author dikmax
 * @version
 */
class App_Db_Table_MailThread extends Zend_Db_Table_Abstract
{
    /**
     * The default table name
     */
    protected $_name = 'lib_mail_thread';

    /**
     * Primery key
     */
    protected $_primary = 'lib_mail_thread_id';

    /**
     * This table supports auto-incremental key
     */
    protected $_sequence = true;

    /**
     * Foreign keys
     */
    protected $_referenceMap = array(
        'User1' => array(
            'columns'           => 'user1_id',
            'refTableClass'     => 'App_Db_Table_User',
            'refColumns'        => 'lib_user_id'
        ),
        'User2' => array(
            'columns'           => 'user2_id',
            'refTableClass'     => 'App_Db_Table_User',
            'refColumns'        => 'lib_user_id'
        )
    );

    /**
     * Dependent tables
     */
    protected $_dependentTables = array(
        'App_Db_Table_MailMessage'
    );

    public function getThreadsList($userId, $state)
    {
        $select = $this->getAdapter()->select()
            ->from($this->_name)
            ->where('user1_id = ?', $userId)
            ->where('state_user1 = ?', $state)
            ->orWhere('user2_id = ?', $userId)
            ->where('state_user2 = ?', $state);

        return $this->getAdapter()->fetchAll($select);
    }
}
