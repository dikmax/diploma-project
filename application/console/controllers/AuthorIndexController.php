<?php
require_once ('application/default/models/App/Console/Controller/Action/Abstract.php');
class AuthorIndexController extends App_Console_Controller_Action_Abstract
{
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
     *
     * @see App_Console_Controller_Action_Abstract::process()
     */
    public function process ()
    {}
}
?>