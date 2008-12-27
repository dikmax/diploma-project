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
 * Delete friend request confirmation
 */
class App_Form_Friends_ConfirmDelete extends Zend_Form
{
    /**
     * Should we disable loading the default decorators?
     * @var bool
     */
    protected $_disableLoadDefaultDecorators = true;

    /**
     * @var Zend_Form_Element_Submit
     */
    protected $_delete;

    /**
     * @var Zend_Form_Element_Submit
     */
    protected $_cancel;

    /**
     * Initialize form
     */
    public function init()
    {
        $this->_delete = new Zend_Form_Element_Submit('delete', array(
            'label' => 'Удалить'
        ));

        $this->_cancel = new Zend_Form_Element_Submit('cancel', array(
            'label' => 'Отменить'
        ));

        $this->addElement($this->_delete)
             ->addElement($this->_cancel);

        $this->setElementDecorators(array(
            array('ViewHelper'),
            array('HtmlTag', array('tag' => 'li'))
        ));

        $this->addDecorator('FormElements')
             ->addDecorator('HtmlTag', array('tag' => 'ul', 'class' => 'buttons-list'))
             ->addDecorator('Form');
    }
}
