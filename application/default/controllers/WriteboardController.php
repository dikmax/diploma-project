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
    }

    /**
     * Returns writeboard messages
     */
    public function ajaxGetAction()
    {
        $this->initAjax();

        $id = $this->getRequest()->getParam('id');
        if (!is_numeric($id)) {
            $this->view->ajax = array('success' => false);
            return;
        }

        $user = App_User_Factory::getSessionUser();
        if ($user) {
            $writeboard = $user->getWriteboard();
            if ($writeboard->getId() != $id) {
                $writeboard = new App_Writeboard(array('id' => $id));
            }
        }
        $messages = $writeboard->getMessages();

        $result = array();
        foreach ($messages as $message) {
            $result[] = array(
                'id' => $message->getId(),
                'login' => $message->getWriteboardWriter()->getLogin(),
                'deleteAllowed' => $this->view->isAllowed($message, 'delete'),
                'date' => $message->getMessageDate()->toRelativeString(),
                'message' => $message->getHtml()
            );
        }

        $this->view->ajax = array(
            'success' => true,
            'messages' => $result
        );
    }

    public function ajaxAddAction()
    {
        $this->initAjax();

        $id = $this->getRequest()->getParam('id');
        if (!is_numeric($id)) {
            $this->view->ajax = array('success' => false);
            return;
        }
        $messageText = $this->getRequest()->getParam('message');

        $user = App_User_Factory::getSessionUser();
        if ($user) {
            $writeboard = $user->getWriteboard();
            if ($writeboard->getId() != $id) {
                $writeboard = new App_Writeboard(array('id' => $id));
            }
        }

        $message = $writeboard->addMessage($messageText);

        $this->view->ajax = array(
            'success' => true,
            'message' => array (
                'id' => $message->getId(),
                'login' => $message->getWriteboardWriter()->getLogin(),
                'deleteAllowed' => $this->view->isAllowed($message, 'delete'),
                'date' => $message->getMessageDate()->toRelativeString(),
                'message' => $message->getHtml()
            )
        );
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