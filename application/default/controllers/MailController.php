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
            $this->_redirect($this->_helper->url->url(array(
                'controller' => 'auth',
                'action' => 'login')));
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
            $this->view->url(array(
                'action' => 'active',
                'param' => ''
            )));
        $this->_topMenu->addItem('sent', 'Отправленые',
            $this->view->url(array(
                'action' => 'sent',
                'param' => ''
            )));
        $this->_topMenu->addItem('archive', 'Архив',
            $this->view->url(array(
                'action' => 'archive',
                'param' => ''
            )));
        $this->_topMenu->addItem('friend-requests', 'Предложения дружбы',
            $this->view->url(array(
                'action' => 'friend-requests',
                'param' => ''
            )));
        $this->_topMenu->addItem('new', 'Новое сообщение',
            $this->view->url(array(
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
        $form->setAction($this->_helper->url->url());

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                $mail = $this->_user->getMail();
                $mail->createNewThread($form->getRecipientUser(),
                    $form->getValue('subject'), $form->getValue('message'));

                $this->_redirect($this->_helper->url->url(array(
                    'action' => 'sent',
                    'param' => ''
                )));
                // TODO show sent done message
            }
        }
        if (isset($_GET['to'])) {
            $form->getElement('recipient')->setValue($_GET['to']);
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

        $form = new App_Form_Mail_Reply();
        $youFirst = $thread->getUser1Id() == $this->_user->getId();

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                // If thread contains only your messages do not move it to active folder
                $messages = $thread->getMessages();
                $yourMessagesOnly = true;
                foreach ($messages as $message) {
                    if ($message['from_user1'] != $youFirst) {
                        $yourMessagesOnly = false;
                        break;
                    }
                }
                $thread->addMessage($youFirst,
                    $form->getValue('message'), !$yourMessagesOnly);

                $this->_redirect($this->_helper->url->url(array(
                    'action' => $yourMessagesOnly ? 'sent' : 'active',
                    'param' => ''
                )));
            }
        }

        $this->view->youFirst = $youFirst;
        $this->view->name1 = $thread->getUser1()->getLogin();
        $this->view->name2 = $thread->getUser2()->getLogin();
        $this->view->thread = $thread;
        $this->view->messages = $thread->getMessages();

        $thread->markAsRead(!$youFirst);
        $this->view->form = $form;

        $this->_helper->viewRenderer->setScriptAction('show-thread');
    }

    /**
     * Redirect to friend requests
     */
    public function friendRequestsAction()
    {
        $this->_topMenu->selectItem('friend-requests');

        $this->_forward('requests', 'friends');
    }
}
