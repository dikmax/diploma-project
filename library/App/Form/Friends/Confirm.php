<?php
/**
 * Books social network
 *
 * LICENSE: Closed source
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id$
 */

require_once 'Zend/Form.php';
require_once 'Zend/Form/Decorator/Form.php';
require_once 'Zend/Form/Decorator/FormElements.php';
require_once 'Zend/Form/Decorator/HtmlTag.php';
require_once 'Zend/Form/Decorator/ViewHelper.php';
require_once 'Zend/Form/Element/Submit.php';

/**
 * Send friendship request confirmation
 */
class App_Form_Friends_Confirm extends Zend_Form
{
    /**
     * Should we disable loading the default decorators?
     * @var bool
     */
    protected $_disableLoadDefaultDecorators = true;

    /**
     * @var Zend_Form_Element_Submit
     */
    protected $_confirm;

    /**
     * @var Zend_Form_Element_Submit
     */
    protected $_decline;



    /**
     * Initialize form
     */
    public function init()
    {
        $this->_confirm = new Zend_Form_Element_Submit('confirm', array(
            'label' => 'Продолжить'
        ));

        $this->_decline = new Zend_Form_Element_Submit('decline', array(
            'label' => 'Отменить'
        ));

        $this->addElement($this->_confirm)
             ->addElement($this->_decline);

        $this->setElementDecorators(array(
            array('ViewHelper'),
            array('HtmlTag', array('tag' => 'li'))
        ));

        $this->addDecorator('FormElements')
             ->addDecorator('HtmlTag', array('tag' => 'ul', 'class' => 'buttons-list'))
             ->addDecorator('Form');
    }
}
