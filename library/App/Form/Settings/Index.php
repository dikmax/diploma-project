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
require_once 'Zend/Form/Element/Radio.php';
require_once 'Zend/Form/Element/Submit.php';
require_once 'Zend/Form/Element/Text.php';
require_once 'Zend/Form/Element/Textarea.php';

class App_Form_Settings_Index extends App_Form_Table
{
    /**
     * @var Zend_Form_Element_Text
     */
    protected $_realName;

    /**
     * @var Zend_Form_Element_Radio
     */
    protected $_sex;

    /**
     * @var Zend_Form_Element_Textarea
     */
    protected $_info;

    /**
     * @var Zend_Form_Element_Submit
     */
    protected $_submit;

    protected function initForm()
    {
        if ($this->getAction() === '') {
            $this->setAction('/settings');
        }
        $this->setMethod('post');
    }

    protected function initElements()
    {
        $this->_realName = new Zend_Form_Element_Text('real_name', array(
            'label' => 'Имя'
        ));

        $this->_sex = new Zend_Form_Element_Radio('sex', array(
            'label' => 'Пол',
            'separator' => '&nbsp;',
            'multiOptions' => array(
                '1' => 'Мужской',
                '2' => 'Женский',
                '0' => 'Не указан'
            )
        ));

        $this->_info = new Zend_Form_Element_Textarea('about', array(
            'label' => 'О себе',
            'rows' => 4
        ));

        $this->_submit = new Zend_Form_Element_Submit('submit', array(
            'label' => 'Сохранить',
        ));

        $this->addElement($this->_realName)
             ->addElement($this->_sex)
             ->addElement($this->_info)
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