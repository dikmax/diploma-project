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
        $this->_helper->Breadcrumbs('/index/' ,'Index module');
    }
    
    /**
     * Main page controller
     */
    public function indexAction()
    {
        $this->_helper->Breadcrumbs('/index/index/', 'Index action');
        
        $config = Zend_Registry::get('config');
        $channel = App_Channel::read($config->mainChannelId);
        $this->view->items = $channel->getItems();
        
        $this->view->vars = array(
            "title" => "Librarian",
            "main" => "Тест!");

        //$text = new App_Text(array("text" => "Тест"));
        //$text->write();
        
        //$this->view->rows = $r;
    }
    
    /**
     * Main library page
     */
    public function libraryAction()
    {
        
    }
    
    public function textReadAction()
    {
        $text = App_Text::read(9);
        
        echo $text->getRevision()->getText();
        die;
    }
}
