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
 * Interface for tag cloud reading
 */
interface App_Tag_Cloud_Reader_Interface
{
    /**
     * Returns tag cloud list
     *
     * @return App_Tag_Cloud_Collection
     */
    public function readTagCloudList();
}