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
 * Index controller. Moslty for main page
 *
 * @copyright  2008 Maxim Dikun
 * @version    Release: 0.0.1
 */
class IndexController extends Zend_Controller_Action
{
    /**
     * Initialization of controller
     */
    public function init()
    {
    }

    /**
     * Main page controller
     */
    public function indexAction()
    {
        $this->view->authors = App_Library::getMostReadAuthors();
        $this->view->titles = App_Library::getMostReadTitles();
    }

    public function textReadAction()
    {
        $text = App_Text::read(9);

        echo $text->getRevision()->getText();
        die;
    }
}
