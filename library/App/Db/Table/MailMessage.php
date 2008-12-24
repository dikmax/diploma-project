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
 * Mail message table model
 *
 * @author dikmax
 * @version
 */
class App_Db_Table_MailMessage extends Zend_Db_Table_Abstract
{
    /**
     * The default table name
     *
     * @var string
     */
    protected $_name = 'lib_mail_message';

    /**
     * Primery key
     *
     * @var string
     */
    protected $_primary = 'lib_mail_message_id';

    /**
     * This table supports auto-incremental key
     *
     * @var boolean
     */
    protected $_sequence = true;

    /**
     * Foreign keys
     *
     * @var array
     */
    protected $_referenceMap = array(
        'MailThread' => array(
            'columns'           => 'lib_mail_thread_id',
            'refTableClass'     => 'App_Db_Table_MailThread',
            'refColumns'        => 'lib_mail_thread_id'
        )
    );

    /**
     * Returns thread messages
     *
     * @param int $threadId
     *
     * @return array
     */
    public function getThreadMessages($threadId) {
        $select = $this->_db->select()
            ->from($this->_name)
            ->where('lib_mail_thread_id = :thread_id')
            ->order(new Zend_Db_Expr('`date` DESC'));

        return $this->_db->fetchAll($select, array(
            ':thread_id' => $threadId
        ));
    }
}
