<?php
/**
 * Books social network
 *
 * LICENSE: Closed source
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id$
 */

require_once 'App/Collection.php';

/**
 * App_Tag_Cloud_Collection description
 */
class App_Tag_Cloud_Collection extends App_Collection
{
    /**
     * Return tag weights minimum and maximum
     *
     * @return array
     *
     * @throws App_Tag_Cloud_Collection_Exception
     */
    public function getMinMax()
    {
        $min = 2147483647;
        $max = 0;
        for ($i = $this->getIterator(); $i->valid(); $i->next()) {
            $tag = $i->current();
            if (!($tag instanceof App_Tag_Data)) {
                require_once 'App/Tag/Cloud/Collection/Exception.php';
                throw new App_Tag_Cloud_Collection_Exception(
                    "App_Tag_Cloud_Collection can contains only instances of App_Tag_Data");
            }
            if ($tag->count < $min) {
                $min = $tag->count;
            }
            if ($tag->count > $max) {
                $max = $tag->count;
            }
        }

        return array($min, $max);
    }
}
?>