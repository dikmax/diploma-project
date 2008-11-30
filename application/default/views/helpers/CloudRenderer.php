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
 * Helper for generating tag clouds (tag clouds writer)
 */
class App_View_Helper_CloudRenderer extends Zend_View_Helper_Abstract implements App_Tag_Cloud_Writer_Interface
{
    /**
     * Array of result strings
     *
     * @var array
     */
    private $_tags;

    // TODO link url

    /**
     * Constructs helper
     */
    public function __construct()
    {
        $this->_tags = array();
    }

    /**
     * Main render function
     */
    public function cloudRenderer()
    {
        return implode(' ', $this->_tags);
    }

    /**
     * @see App_Tag_Cloud_Writer_Interface::writeTagCloudEmpty()
     */
    public function writeTagCloudEmpty()
    {
    }

    /**
     * @see App_Tag_Cloud_Writer_Interface::writeTagCloudEnd()
     */
    public function writeTagCloudEnd()
    {
    }

    /**
     * @see App_Tag_Cloud_Writer_Interface::writeTagCloudItem()
     *
     * @param string $name
     * @param int $weight
     * @param int $count
     */
    public function writeTagCloudItem($name, $weight, $count)
    {
        $this->_tags[] = '<span title="Использован ' . $count
            . ' раз" style="font-size: '. ($weight * 2 + 100) . '%">' . $name . '</span>';
    }

    /**
     * @see App_Tag_Cloud_Writer_Interface::writeTagCloudStart()
     */
    public function writeTagCloudStart()
    {
    }



}