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
 * Mail reply form
 */
class App_Form_Mail_Reply extends Zend_Form
{
    /**
     * @var Zend_Form_Element_Textarea
     */
    protected $_message;

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

        $this->setAttrib('class', 'message-reply');

        $this->_message = new Zend_Form_Element_Textarea('message', array(
            'required' => true,
            'rows' => 5
        ));

        // Submit
        $this->_submit = new Zend_Form_Element_Submit('submit', array(
            'label' => 'Ответить'
        ));

        // Form
        $this->addElement($this->_message)
             ->addElement($this->_submit);

        $this->setElementDecorators(array(
            array('ViewHelper'),
            array('Errors'),
            array('Description', array('tag' => 'p', 'class' => 'description')),
            array('HtmlTag', array('tag' => 'dd'))
        ));
    }
}
