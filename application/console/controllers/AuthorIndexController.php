<?php
class AuthorIndexController extends App_Console_Controller_Action_Abstract
{
    /**
     *
     * @see App_Console_Controller_Action_Abstract::process()
     */
    public function process ()
    {
        echo "Author index build start...\n";
        echo "Author index build end...\n";
    }

    /**
     *
     * @return string
     * @see App_Console_Controller_Action_Abstract::getLongActionName()
     */
    public static function getLongActionName ()
    {
        return 'author_index';
    }

    /**
     * @see App_Console_Controller_Action_Abstract::getShortActionName()
     *
     * @return string
     */
    public static function getShortActionName ()
    {
        return 'a';
    }

    /**
     * @see App_Console_Controller_Action_Abstract::getDescription()
     *
     * @return string
     */
    public static function getDescription ()
    {
        return 'Update authors name index';
    }
}
?>