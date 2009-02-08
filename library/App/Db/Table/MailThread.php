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
 * Mail thread table model
 *
 * @author dikmax
 * @version
 */
class App_Db_Table_MailThread extends App_Db_Table_Abstract
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

    /**
     * Returns list of threads
     *
     * @param int $userId user
     * @param int $state thread state
     *
     * @return array
     */
    public function getThreadsList($userId, $state)
    {
        $select = $this->_db->select()
            ->from($this->_name)
            ->where('user1_id = :user1_id')
            ->where('state_user1 = :state_user1')
            ->orWhere('user2_id = :user2_id')
            ->where('state_user2 = :state_user2')
            ->order(new Zend_Db_Expr('`date` DESC'));


        return $this->_db->fetchAll($select, array(
            ':user1_id' => $userId,
            ':state_user1' => $state,
            ':user2_id' => $userId,
            ':state_user2' => $state
        ));
    }

    /**
     * Return specific thread id
     *
     * @param int $userId
     * @param int $threadId
     *
     * @return array
     */
    public function getThread($userId, $threadId)
    {
        $select = $this->_db->select()
            ->from($this->_name)
            ->where('lib_mail_thread_id = :thread_id')
            ->where('user1_id = :user1_id OR user2_id = :user2_id');

        return $this->_db->fetchRow($select, array(
            ':user1_id' => $userId,
            ':user2_id' => $userId,
            ':thread_id' => $threadId
        ));
    }
}
