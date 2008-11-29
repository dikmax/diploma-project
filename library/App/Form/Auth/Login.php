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
 * Auth login form
 */
class App_Form_Auth_Login extends Zend_Form
{
    /**
     * Constructs registration form
     *
     * @param string $action
     */
    public function __construct($action = '/auth/login')
    {
        parent::__construct();

        // Creating elements
        $login = new Zend_Form_Element_Text('login', array(
            'label' => 'Логин',
            'required' => true,
            'disableLoadDefaultDecorators' => true
        ));

        $password = new Zend_Form_Element_Password('password', array(
            'label' => 'Пароль',
            'required' => true,
            'disableLoadDefaultDecorators' => true
        ));

        $submit = new Zend_Form_Element_Submit('submit', array(
            'label' => 'Ага, это я!',
            'disableLoadDefaultDecorators' => true
        ));

        // Creating form
        $this->setAction($action)
             ->setMethod('post');

        $this->addElement($login)
             ->addElement($password)
             ->addElement($submit);

        // Applying table formatting
        $this->setDecorators(array(
            'FormElements',
            array(
                'decorator' => array('table' => 'HtmlTag'),
                'options' => array('tag' => 'table')
            ),
            'Form',
            new Zend_Form_Decorator_FormErrors(array(
                'placement' => Zend_Form_Decorator_Abstract::PREPEND
            )),
            array(
                'decorator' => array('div' => 'HtmlTag'),
                'options' => array('tag' => 'div', 'class' => 'login')
            )
        ));
        $this->setElementDecorators(array(
            array('ViewHelper'),
            array(
                'decorator' => array('td' => 'HtmlTag'),
                'options' => array('tag' => 'td')
            ),
            new App_Form_Decorator_Label(array(
                'tag' => 'td',
                'tagClass' => 'label',
                'requiredSuffix' => ':'
            )),
            array(
                'decorator' => array('tr' => 'HtmlTag'),
                'options' => array('tag' => 'tr')
            ),
        ));

        // Set custom elements decorators
        $submit->setDecorators(array(
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
?>