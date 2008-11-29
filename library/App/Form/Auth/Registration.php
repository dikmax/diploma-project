<?php
/**
 * Books social network
 *
 * LICENSE: Closed source
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id:$
 */

/**
 * Auth registration form
 */
class App_Form_Auth_Registration extends Zend_Form
{
    /**
     * Constructs registration form
     */
    public function __construct()
    {
        parent::__construct();

        $this->setMethod('post');

        // Login
        $login = new Zend_Form_Element_Text('login', array(
            'label' => 'Логин',
            'required' => true
        ));
        $login->addFilter(new Zend_Filter_StringToLower())
              ->addValidator(new Zend_Validate_StringLength(6, 30));

        // Password
        $password = new Zend_Form_Element_Password('password', array(
            'label' => 'Пароль',
            'required' => true
        ));
        $password->addValidator(new Zend_Validate_StringLength(6));

        // Password retype
        $passwordRetype = new Zend_Form_Element_Password('passwordretype', array(
            'label' => 'Еще раз',
            'required' => true
        ));
        $password->addValidator(new Zend_Validate_StringLength(6));

        $email = new Zend_Form_Element_Text('email', array(
            'label' => 'E-mail',
            'required' => true
        ));
        $email->addValidator(new Zend_Validate_EmailAddress(Zend_Validate_Hostname::ALLOW_DNS, true));

        // Submit
        $submit = new Zend_Form_Element_Submit('submit', array(
            'label' => 'Зарегистрироваться'
        ));

        // Form
        $this->addElement($login)
             ->addElement($password)
             ->addElement($passwordRetype)
             ->addElement($email)
             ->addElement($submit);
    }

    /**
     * @see Zend_Form::isValid()
     *
     * @param array $data
     * @return boolean
     */
    public function isValid($data)
    {
        $result = parent::isValid($data);

        if ($result === true) {
            // Checking for duplicate login
            $userFactory = App_User_Factory::getInstance();
            try {
                echo $this->getValue('login');
                $userFactory->getUserByLogin($this->getValue('login'));
                $exists = true;
            } catch (App_User_Exception $e) {
                $exists = false;
            }
            if ($exists) {
                $this->getElement('login')->addError('Пользователь с таким логином уже существует');
                $result = false;
            }

            // Checking for duplicate email
            try {
                $userFactory->getUserByEmail($this->getValue('email'));
                $exists = true;
            } catch (App_User_Exception $e) {
                $exists = false;
            }

            if ($exists) {
                $this->getElement('email')->addError('Данный email уже используется');
                $result = false;
            }

            if ($this->getValue('password') !== $this->getValue('passwordretype')) {
                $this->getElement('passwordretype')->addError('Введеные пароли не совпадают.');
                $result = false;
            }
        }
        return $result;
    }
}
?>