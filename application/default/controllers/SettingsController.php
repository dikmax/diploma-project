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
 * Settings controller
 *
 * @copyright  2008 Maxim Dikun
 * @author Dikun Maxim
 */
class SettingsController extends Zend_Controller_Action
{
    /**
     * Current user
     *
     * @var App_User
     */
    protected $_user;

    /**
     * Init controller
     */
    public function init()
    {
        require_once 'App/User/Factory.php';
        $this->_user = App_User_Factory::getSessionUser();
        if (!$this->_user) {
            // Not logged in. Redirect to login page
            $this->_redirect($this->view->url(array(
                'controller' => 'auth',
                'action' => 'login'
            )));
            return;
        }
    }

    /**
     * The default action - show the main settings page
     */
    public function indexAction()
    {
        require_once 'App/Form/Settings/Index.php';
        $form = new App_Form_Settings_Index($this->view->url());

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                if ($form->getValue('remove_userpic')) {
                    $this->_user->setUserpic(false);
                } else if ($form->userpic->isUploaded()) {
                    $form->userpic->receive();
                    $this->_user->setUserpic(true);
                }

                $this->_user->setRealName($form->getValue('real_name'));
                $this->_user->setSex((int)$form->getValue('sex'));
                $this->_user->setAbout($form->getValue('about'));

                $this->_user->write();

                $this->view->saveDone = true;
            }
        }

        $form->setDefaults(array(
            'userpic' => $this->_user->getUserpicUrl(),
            'remove_userpic' => false,
            'real_name' => $this->_user->getRealName(),
            'sex' => $this->_user->getSex(),
            'about' => $this->_user->getAbout()
        ));

        $this->view->form = $form;
    }
}