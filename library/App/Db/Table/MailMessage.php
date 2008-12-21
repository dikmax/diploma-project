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
     */
    protected $_name = 'lib_mail_message';

    /**
     * Primery key
     */
    protected $_primary = 'lib_mail_message_id';

    /**
     * This table supports auto-incremental key
     */
    protected $_sequence = true;

    /**
     * Foreign keys
     */
    protected $_referenceMap = array(
        'MailThread' => array(
            'columns'           => 'lib_mail_thread_id',
            'refTableClass'     => 'App_Db_Table_MailThread',
            'refColumns'        => 'lib_mail_thread_id'
        )
    );
}
