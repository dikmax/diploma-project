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
require_once 'Zend/Filter/StringTrim.php';
require_once 'Zend/Form/Decorator/HtmlTag.php';
require_once 'Zend/Form/Decorator/ViewHelper.php';
require_once 'Zend/Form/Element/Submit.php';
require_once 'Zend/Form/Element/Text.php';
require_once 'Zend/Form/Element/Textarea.php';

/**
 * New mail form
 */
class App_Form_Mail_New extends App_Form_Table
{
    /**
     * @var Zend_Form_Element_Text
     */
    protected $_recipient;

    /**
     * @var Zend_Form_Element_Text
     */
    protected $_subject;

    /**
     * @var Zend_Form_Element_Textarea
     */
    protected $_message;

    /**
     * @var Zend_Form_Element_Submit
     */
    protected $_submit;

    /**
     * @var App_User
     */
    protected $_recipientUser = false;

    /**
     * @see App_Form_Table::initElements()
     */
    protected function initElements()
    {
        $trimFilter = new Zend_Filter_StringTrim();

        $this->_recipient = new Zend_Form_Element_Text('recipient', array(
            'label' => 'Кому',
            'required' => true
        ));
        $this->_recipient
            ->addFilter($trimFilter);

        $this->_subject = new Zend_Form_Element_Text('subject', array(
            'label' => 'Тема',
            'required' => true
        ));
        $this->_subject
            ->addFilter($trimFilter);

        $this->_message = new Zend_Form_Element_Textarea('message', array(
            'label' => 'Сообщение',
            'required' => true
        ));

        $this->_submit = new Zend_Form_Element_Submit('submit', array(
            'label' => 'Отправить'
        ));

        $this->addElement($this->_recipient)
             ->addElement($this->_subject)
             ->addElement($this->_message)
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
            // TODO rewrite on validators
            $currentUser = App_User_Factory::getSessionUser();
            if (!$currentUser) {
                return false;
            }
            if ($this->getValue('recipient') === $currentUser->getLogin()) {
                $this->_recipient->addError('Нельзя отправить письмо себе.');
                return false;
            }
            if ($this->getRecipientUser() === null) {
                $this->_recipient->addError('Такого пользователя не существует');
                return false;
            }
        }

        return $result;
    }

    /**
     * Return recipient user
     */
    public function getRecipientUser()
    {
        if ($this->_recipientUser === false) {
            $this->_recipientUser = App_User_Factory::getInstance()
                ->getUserByLogin($this->getValue('recipient'));
        }
        return $this->_recipientUser;
    }
}
