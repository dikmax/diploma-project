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
 * Writeboard controller.
 *
 * @copyright  2008 Maxim Dikun
 * @version    Release: 0.0.1
 */
class WriteboardController extends App_Controller_AjaxAction
{
    /**
     * Prepares and shows writeboard
     */
    public function showAction()
    {
        $writeboard = $this->getRequest()->getParam('writeboard');

        $this->view->id = $writeboard->getId();
        $this->view->messages = $writeboard->getMessages();
    }

    /**
     * Adds new message to writeboard
     */
    public function addAction()
    {
        $id = $this->getRequest()->getParam('id');
        $message = $this->getRequest()->getParam('message');

        $writeboard = new App_Writeboard(array('lib_writeboard_id' => $id));

        $writeboard->addMessage($message);
    }

    /**
     * Ajax delete
     */
    public function ajaxDeleteAction()
    {
        $this->initAjax();

        try {
            $id = $this->getRequest()->getParam('id');
            $messageid = $this->getRequest()->getParam('messageid');

            if (is_numeric($id) && is_numeric($messageid)) {
                $writeboard = new App_Writeboard(array('lib_writeboard_id' => $id));

                $writeboard->removeMessage($messageid);

                $this->view->ajax = array('success' => true);
            } else {
                $this->view->ajax = array('success' => false);
            }
        } catch (Exception $e) {
            $this->view->ajax = array(
                'success' => false,
                'message' => $e->getMessage()
            );
        }
    }
}