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
 * Blog controller.
 *
 * @copyright  2008 Maxim Dikun
 * @version    Release: 0.0.1
 */
class BlogController extends Zend_Controller_Action
{
    public function newAction()
    {
        
    }
    
    public function showAction()
    {
        $this->view->headTitle('журнал');
        $login = $this->getRequest()->getParam('login');
        $this->view->login = $login;
        
        $this->view->count = 0;
    }
}