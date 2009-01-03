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
 * Action controller with ajax support
 */
class App_Controller_AjaxAction extends Zend_Controller_Action
{
    /**
     * Initializes ajax
     */
    protected function initAjax()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoController();
        $this->_helper->viewRenderer->setScriptAction('ajax');
    }
}
