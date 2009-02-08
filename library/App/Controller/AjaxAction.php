<?php
/**
 * Books social network
 *
 * LICENSE: Closed source
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id$
 */

require_once 'Zend/Controller/Action.php';

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

    /**
     * Set succeded result of ajax request
     *
     * @param array $result
     */
    protected function success(array $result = array())
    {
        $result['success'] = true;

        $this->view->ajax = $result;
    }

    /**
     * Set failed ajax result
     *
     * @param string $message
     */
    protected function fail($message = null)
    {
        $result = array('success' => false);
        if ($message !== null) {
            $result['message'] = $message;
        }

        $this->view->ajax = $result;
    }
}
