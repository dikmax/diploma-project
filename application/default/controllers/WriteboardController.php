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
            $this->fail();
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

        $this->success(array(
            'messages' => $result
        ));
    }

    public function ajaxAddAction()
    {
        $this->initAjax();

        $id = $this->getRequest()->getParam('id');
        if (!is_numeric($id)) {
            $this->fail();
            return;
        }
        $messageText = $this->getRequest()->getParam('message');
        $messageText = Zend_Filter::get($messageText, 'StringTrim');
        $lengthValidator = new Zend_Validate_StringLength(1,1000);
        if (!$lengthValidator->isValid($messageText)) {
            $this->fail();
            return;
        }

        $user = App_User_Factory::getSessionUser();
        if ($user) {
            $writeboard = $user->getWriteboard();
            if ($writeboard->getId() != $id) {
                $writeboard = new App_Writeboard(array('id' => $id));
            }
        }

        $message = $writeboard->addMessage($messageText);

        $this->success(array(
            'message' => array (
                'id' => $message->getId(),
                'login' => $message->getWriteboardWriter()->getLogin(),
                'deleteAllowed' => $this->view->isAllowed($message, 'delete'),
                'date' => $message->getMessageDate()->toRelativeString(),
                'message' => $message->getHtml()
            )
        ));
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

                $this->success();
            } else {
                $this->fail();
            }
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }
    }
}