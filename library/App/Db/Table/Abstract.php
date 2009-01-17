<?php
/**
 * Books social network
 *
 * LICENSE: Closed source
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id:$
 */

/**
 * Abstract table model
 *
 * @author dikmax
 * @version
 */
class App_Db_Table_Abstract extends Zend_Db_Table_Abstract
{
    /**
     * Instances counter for detecting memory leaks
     *
     * @var int
     */
    public static $instancesCount = 0;

    public function __construct($config = array())
    {
        parent::__construct($config);

        self::$instancesCount++;
    }

    public function __destruct()
    {
        self::$instancesCount--;
    }
}
