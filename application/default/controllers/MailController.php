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
 * Mail controller.
 *
 * @copyright  2008 Maxim Dikun
 * @version    Release: 0.0.1
 */
class MailController extends Zend_Controller_Action
{
    /**
     * Current user
     *
     * @var App_User
     */
    protected $_user;

    /**
     * Top menu
     *
     * @var App_View_Helper_TopMenu
     */
    protected $_topMenu;

    /**
     * Initialize controller
     */
    public function init()
    {
        $this->_user = App_User_Factory::getSessionUser();
        if (!$this->_user) {
            // Not logged in. Redirect to login page
            $this->_redirect($this->_helper->url->url(array('action' => 'login'), 'auth'));
            return;
        }

        if ($this->_user) {
            $this->initMenu();
        }
    }

    /**
     * Initialize menu
     */
    protected function initMenu()
    {
        $this->_topMenu = $this->view->getHelper('topMenu');

        $this->_topMenu->addItem('active', 'Активные',
            $this->_helper->url->url(array(
                'action' => 'active',
                'param' => ''
            )));
        $this->_topMenu->addItem('sent', 'Отправленые',
            $this->_helper->url->url(array(
                'action' => 'sent',
                'param' => ''
            )));
        $this->_topMenu->addItem('archive', 'Архив',
            $this->_helper->url->url(array(
                'action' => 'archive',
                'param' => ''
            )));
        $this->_topMenu->addItem('new', 'Новое сообщение',
            $this->_helper->url->url(array(
                'action' => 'new',
                'param' => ''
            )));
    }

    /**
     * Show active threads action
     */
    public function activeAction()
    {
        if ($this->getRequest()->getParam('param')) {
            $this->showThreadAction($this->getRequest()->getParam('param'));
            return;
        }
        $this->_topMenu->selectItem('active');

        $mail = $this->_user->getMail();
        $this->view->threads = $mail->getThreadsList(App_Mail_Thread::STATE_ACTIVE);
    }

    /**
     * Show sent threads action
     */
    public function sentAction()
    {
        if ($this->getRequest()->getParam('param')) {
            $this->showThreadAction($this->getRequest()->getParam('param'));
            return;
        }
        $this->_topMenu->selectItem('sent');

        $mail = $this->_user->getMail();
        $this->view->threads = $mail->getThreadsList(App_Mail_Thread::STATE_SENT);
    }

    /**
     * Show archive threads action
     */
    public function archiveAction()
    {
        if ($this->getRequest()->getParam('param')) {
            $this->showThreadAction($this->getRequest()->getParam('param'));
            return;
        }
        $this->_topMenu->selectItem('archive');

        $mail = $this->_user->getMail();
        $this->view->threads = $mail->getThreadsList(App_Mail_Thread::STATE_ARCHIVE);
    }

    /**
     * Compose new mail action
     */
    public function newAction()
    {
        $this->_topMenu->selectItem('new');

        $form = new App_Form_Mail_New();

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                $mail = $this->_user->getMail();
                $mail->createNewThread($form->getRecipientUser(),
                    $form->getValue('subject'), $form->getValue('message'));

                $this->_redirect($this->_helper->url->url(array('action' => 'sent')));
                // TODO show sent done message
            }
        }
        $this->view->form = $form;
    }

    /**
     * Shows specific thread
     *
     * @param int $threadId
     */
    protected function showThreadAction($threadId)
    {
        $mail = $this->_user->getMail();

        $thread = $mail->getThread($threadId);
        if (!$thread) {
            $this->_forward('not-allowed', 'error');
            return;
        }

        $this->view->youFirst = $thread->getUser1Id() == $this->_user->getId();
        $this->view->name1 = $thread->getUser1()->getLogin();
        $this->view->name2 = $thread->getUser2()->getLogin();
        $this->view->thread = $thread;

        $this->_helper->viewRenderer->setScriptAction('show-thread');
    }
}
