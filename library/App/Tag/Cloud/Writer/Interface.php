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
 * Interface for tag cloud writing
 */
interface App_Tag_Cloud_Writer_Interface
{
    /**
     * Executed just before writing tag cloud
     */
    public function writeTagCloudStart();

    /**
     * Writes tag cloud single item
     *
     * @param string $name
     * @param int $weight
     * @param int $count
     */
    public function writeTagCloudItem($name, $weight, $count);

    /**
     * Executes when there's no items in cloud
     */
    public function writeTagCloudEmpty();

    /**
     * Executes when all items is written
     */
    public function writeTagCloudEnd();
}