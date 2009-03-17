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
        $form->setDefaults(array(
            'userpic' => '/images/default_user.png',
            'real_name' => $this->_user->getRealName(),
            'sex' => $this->_user->getSex(),
            'about' => $this->_user->getAbout()
        ));

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($_POST)) {
                if ($form->userpic->isUploaded()) {
                    $form->userpic->receive();
                    $location = $form->userpic->getFilename();
                }

                $this->_user->setRealName($form->getValue('real_name'));
                $this->_user->setSex((int)$form->getValue('sex'));
                $this->_user->setAbout($form->getValue('about'));

                $this->_user->write();

                $this->view->saveDone = true;
            }
        }

        $this->view->form = $form;
    }
}