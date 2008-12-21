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

        $this->initMenu();
    }

    /**
     * Initialize menu
     */
    protected function initMenu()
    {
        $this->_topMenu = $this->view->getHelper('topMenu');

        $this->_topMenu->addItem('inbox', 'Входящие',
            $this->_helper->url->url(array('action' => 'inbox')));
        $this->_topMenu->addItem('sent', 'Отправленые',
            $this->_helper->url->url(array('action' => 'sent')));
        $this->_topMenu->addItem('new', 'Новое сообщение',
            $this->_helper->url->url(array('action' => 'new')));
    }

    /**
     * Show inbox action
     */
    public function inboxAction()
    {
        $this->_topMenu->selectItem('inbox');
    }

    /**
     * Show sent action
     */
    public function sentAction()
    {
        $this->_topMenu->selectItem('sent');
    }

    /**
     * Compose new mail action
     */
    public function newAction()
    {
        $this->_topMenu->selectItem('new');

        $form = new App_Form_Mail_New();

        $this->view->form = $form;
    }
}
