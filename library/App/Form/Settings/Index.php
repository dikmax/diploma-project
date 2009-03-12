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
require_once 'Zend/Form/Element/Text.php';
require_once 'Zend/Form/Element/Submit.php';

class App_Form_Settings_Index extends App_Form_Table
{
    /**
     * @var Zend_Form_Element_Text
     */
    protected $_realName;

    /**
     * @var Zend_Form_Element_Submit
     */
    protected $_submit;

    protected function initForm()
    {
        if ($this->getAction() === '') {
            $this->setAction('/auth/login');
        }
        $this->setMethod('post');
    }

    protected function initElements()
    {
        $this->_realName = new Zend_Form_Element_Text('realname', array(
            'label' => 'Имя'
        ));

        $this->_submit = new Zend_Form_Element_Submit('submit', array(
            'label' => 'Сохранить',
        ));

        $this->addElement($this->_realName)
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