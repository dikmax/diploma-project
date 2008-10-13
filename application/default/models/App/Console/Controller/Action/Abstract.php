<?php
abstract class App_Console_Controller_Action_Abstract
{
    /**
     * Constructs new console controller object
     */
    public function __construct ()
    {
        // Nothing to do here yet
    }

    /**
     * Inits new controller action object
     */
    public function init()
    {}

    /**
     * Process associated action
     */
    abstract public function process();

    /**
     * Returns long action name
     *
     * @return string
     */
    public static function getLongActionName()
    {
        return '';
    }

    /**
     * Returns short action name
     *
     * @return string
     */
    public static function getShortActionName()
    {
        return '';
    }

    /**
     * Returns action description
     *
     * @return string
     */
    public static function getDescription()
    {
        return '';
    }
}
?>