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
 * App_Tag_Data description
 */
class App_Tag_Data
{
    /**
     * Database id
     *
     * @var int
     */
    public $id;

    /**
     * Tag count/weight
     *
     * @var int
     */
    public $count;

    /**
     * Tag name
     *
     * @var string
     */
    public $name;

    /**
     * Constructs tag
     *
     * @param int $id
     * @param string $name
     * @params int $count
     */
    public function __construct($id = 0, $name = '', $count = 0)
    {
        $this->id = $id;
        $this->name = $name;
        $this->count = $count;
    }
}
?>