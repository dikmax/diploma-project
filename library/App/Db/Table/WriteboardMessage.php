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
 * Writeboard message table model
 *
 * @author dikmax
 * @version
 */
class App_Db_Table_WriteboardMessage extends App_Db_Table_Abstract
{
    /**
     * The default table name
     */
    protected $_name = 'lib_writeboard_message';

    /**
     * Primery key
     */
    protected $_primary = 'lib_writeboard_message_id';

    /**
     * This table supports auto-incremental key
     */
    protected $_sequence = true;

    /**
     * Foreign keys
     */
    protected $_referenceMap = array(
        'User' => array(
            'columns'           => 'writeboard_writer',
            'refTableClass'     => 'App_Db_Table_User',
            'refColumns'        => 'lib_user_id'
        ),
        'Writeboard' => array(
            'columns'           => 'lib_writeboard_id',
            'refTableClass'     => 'App_Db_Table_Writeboard',
            'refColumns'        => 'lib_writeboard_id'
        )
    );

    /**
     * Returns messages from writeboard
     *
     * @param int $writeboardId
     *
     * @return array
     */
    public function getMessages($writeboardId)
    {
        $select = $this->_db->select()
            ->from($this->_name)
            ->where('lib_writeboard_id = :lib_writeboard_id')
            ->order(new Zend_Db_Expr('message_date DESC'));

        return $this->_db->fetchAll($select, array(
            ':lib_writeboard_id' => $writeboardId
        ));
    }

    /**
     * Return messages count for writeboard
     */
    public function getMessagesCount($writeboardId)
    {
        $select = $this->_db->select()
            ->from($this->_name, new Zend_Db_Expr('count(*)'))
            ->where('lib_writeboard_id = :lib_writeboard_id');

        return $this->_db->fetchOne($select, array(
            ':lib_writeboard_id' => $writeboardId
        ));
    }
}
