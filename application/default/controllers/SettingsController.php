<?php
/**
 * Books social network
 *
 * LICENSE: Closed source
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id:$
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
     * The default action - show the main settings page
     */
    public function indexAction()
    {
        $form = new App_Form_Settings_Index();

        $this->view->form = $form;
    }
}