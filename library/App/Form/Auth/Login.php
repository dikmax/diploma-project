<?php
/**
 * Books social network
 *
 * LICENSE: Closed source
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id$
 */

require_once 'App/Form/Table.php';
require_once 'Zend/Form/Decorator/ViewHelper.php';
require_once 'Zend/Form/Decorator/HtmlTag.php';
require_once 'Zend/Form/Element/Text.php';
require_once 'Zend/Form/Element/Password.php';
require_once 'Zend/Form/Element/Submit.php';

/**
 * Auth login form
 */
class App_Form_Auth_Login extends App_Form_Table
{
    /**
     * @var Zend_Form_Element_Text
     */
    protected $_login;

    /**
     * @var Zend_Form_Element_Password
     */
    protected $_password;

    /**
     * @var Zend_Form_Element_Submit
     */
    protected $_submit;

    protected function initForm()
    {
        if ($this->getAction() === '') {
            $this->setAction('/auth/login');
        }
        $this->setMethod('post')
             ->setContainerClass('login')
             ->setNoStarOnRequired(true);
             
    }

    /**
     * @see App_Form_Table::initElements()
     */
    protected function initElements()
    {
        $this->_login = new Zend_Form_Element_Text('login', array(
            'label' => 'Логин',
            'required' => true,
        ));

        $this->_password = new Zend_Form_Element_Password('password', array(
            'label' => 'Пароль',
            'required' => true,
        ));

        $this->_submit = new Zend_Form_Element_Submit('submit', array(
            'label' => 'Ага, это я!',
        ));

        $this->addElement($this->_login)
             ->addElement($this->_password)
             ->addElement($this->_submit);
    }

    /**
     * @see App_Form_Table::initCustomLayout()
     */
    protected function initCustomLayout()
    {
        $this->_submit->setDecorators(array(
            array(
                'decorator' => 'ViewHelper',
                'options' => array('helper' => 'formSubmit')
            ),
            array(
                'decorator' => array('td' => 'HtmlTag'),
                'options' => array('tag' => 'td', 'colspan' => 2)
            ),
            array(
                'decorator' => array('tr' => 'HtmlTag'),
                'options' => array('tag' => 'tr')
            ),
        ));
    }
}
