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
 * Form with table layout
 */
abstract class App_Form_Table extends Zend_Form
{
    /**
     * @var string
     */
    protected $_containerClass;

    public function init()
    {
        $this->initForm();
        $this->initElements();
        $this->initLayout();
        $this->initCustomLayout();
    }

    protected function initForm()
    {

    }

    abstract protected function initElements();

    protected function initLayout()
    {
        $containerOptions = array('tag' => 'div');
        if ($this->_containerClass) {
            $containerOptions['class'] = $this->_containerClass;
        }

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
                'options' => $containerOptions
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
    }

    protected function initCustomLayout()
    {

    }

    public function setContainerClass($containerClass)
    {
        $this->_containerClass = $containerClass;
        return $this;
    }
}
