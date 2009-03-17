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
require_once 'Zend/Form/Decorator/Abstract.php';
require_once 'Zend/Form/Decorator/FormElements.php';
require_once 'Zend/Form/Decorator/FormErrors.php';
require_once 'Zend/Form/Decorator/HtmlTag.php';
require_once 'Zend/Form/Decorator/Label.php';
require_once 'Zend/Form/Decorator/ViewHelper.php';

/**
 * Form with table layout
 */
abstract class App_Form_Table extends Zend_Form
{
    /**
     * @var string
     */
    protected $_containerClass;

    /**
     * Main initialize function
     */
    public function init()
    {
        $this->initForm();
        $this->initElements();
        $this->initLayout();
        $this->initCustomLayout();
    }

    /**
     * Initialize form itself
     */
    protected function initForm()
    {

    }

    /**
     * Initialize form elements
     */
    abstract protected function initElements();

    /**
     * Initialize layout
     */
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
                'requiredSuffix' => '*:',
                'optionalSuffix' => ':'
            )),
            array(
                'decorator' => array('tr' => 'HtmlTag'),
                'options' => array('tag' => 'tr')
            ),
        ));
    }

    /**
     * Custom changes to layout
     */
    protected function initCustomLayout()
    {

    }

    /**
     * Set class to container
     *
     * @param string $containerClass
     *
     * @return App_Form_Table
     */
    public function setContainerClass($containerClass)
    {
        $this->_containerClass = $containerClass;
        return $this;
    }
}
