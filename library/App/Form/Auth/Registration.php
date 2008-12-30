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
 * Auth registration form
 */
class App_Form_Auth_Registration extends Zend_Form
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
     * @var Zend_Form_Element_Password
     */
    protected $_passwordConfirmation;

    /**
     * @var Zend_Form_Element_Text
     */
    protected $_email;

    /**
     * @var Zend_Form_Element_Submit
     */
    protected $_submit;

    /**
     * Initializes form
     */
    public function init()
    {
        $this->setMethod('post');

        // Login
        $this->_login = new Zend_Form_Element_Text('login', array(
            'label' => 'Логин',
            'required' => true
        ));
        $this->_login->addFilter(new Zend_Filter_StringToLower())
             ->addValidator(new Zend_Validate_StringLength(6, 30))
             ->addValidator(new App_Validate_UserNotExists());

        // Password
        $this->_password = new Zend_Form_Element_Password('password', array(
            'label' => 'Пароль',
            'required' => true
        ));
        $this->_password->addValidator(new Zend_Validate_StringLength(6));

        // Password retype
        $this->_passwordConfirmation = new Zend_Form_Element_Password('password-confirmation', array(
            'label' => 'Еще раз',
            'required' => true
        ));
        $this->_passwordConfirmation->addValidator(new Zend_Validate_StringLength(6))
             ->addValidator(new App_Validate_PasswordConfirmation());

        $this->_email = new Zend_Form_Element_Text('email', array(
            'label' => 'E-mail',
            'required' => true
        ));
        $this->_email->addValidator(new Zend_Validate_EmailAddress(Zend_Validate_Hostname::ALLOW_DNS, true))
             ->addValidator(new App_Validate_EmailNotExists());

        // Submit
        $this->_submit = new Zend_Form_Element_Submit('submit', array(
            'label' => 'Зарегистрироваться'
        ));

        // Form
        $this->addElement($this->_login)
             ->addElement($this->_password)
             ->addElement($this->_passwordConfirmation)
             ->addElement($this->_email)
             ->addElement($this->_submit);
    }
}
