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
require_once 'Zend/Filter/File/Rename.php';
require_once 'Zend/Form/Decorator/HtmlTag.php';
require_once 'Zend/Form/Element/Checkbox.php';
require_once 'Zend/Form/Element/File.php';
require_once 'Zend/Form/Element/Radio.php';
require_once 'Zend/Form/Element/Submit.php';
require_once 'Zend/Form/Element/Text.php';
require_once 'Zend/Form/Element/Textarea.php';
require_once 'Zend/Validate/File/Extension.php';
require_once 'Zend/Validate/File/IsImage.php';
require_once 'Zend/Validate/File/Size.php';

class App_Form_Settings_Index extends App_Form_Table
{
    /**
     * @var Zend_Form_Element_File
     */
    protected $_userpic;

    /**
     * @var string
     */
    protected $_temporaryFileName;

    /**
     * @var Zend_Form_Decorator_HtmlTag
     */
    protected $_userpicImageDecorator;

    /**
     * @var Zend_Form_Element_Checkbox
     */
    protected $_removeUserpic;

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

    /**
     * Delete temporary files
     */
    public function __destruct()
    {
        unlink($this->_temporaryFileName);
    }

    protected function initForm()
    {
        //if ($this->getAction() === '') {
        //    $this->setAction('/settings');
        //}
        $this->setMethod('post')
             ->setAttrib('enctype', 'multipart/form-data');
    }

    protected function initElements()
    {
        $this->_temporaryFileName = tempnam(Zend_Registry::get('publicPath') . '/images/userpic/tmp', 'userpic');
        $this->_userpic = new Zend_Form_Element_File('userpic', array(
            'required' => false,
            'label' => 'Картинка'
        ));
        $this->_userpic->addValidators(array(
            new Zend_Validate_File_IsImage(),
            new Zend_Validate_File_Extension(array('jpg', 'png', 'gif')),
            new Zend_Validate_File_Size(524288)
        ));
        $this->_userpic->addFilters(array(
            new Zend_Filter_File_Rename(array(
                'target' => $this->_temporaryFileName,
                'overwrite' => true
            )),
            new App_Filter_File_ImageThumbnail()
        ));


        $this->_removeUserpic = new Zend_Form_Element_Checkbox('remove_userpic', array(
            'label' => 'Удалить картинку'
        ));

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

        $this->addElements(array(
            $this->_userpic,
            $this->_removeUserpic,
            $this->_realName,
            $this->_sex,
            $this->_info,
            $this->_submit
        ));
    }

    /**
     * @see App_Form_Table::initCustomLayout()
     */
    protected function initCustomLayout()
    {
        // Userpic
        $this->_userpicImageDecorator = new Zend_Form_Decorator_HtmlTag(array(
            'tag' => 'img',
            'placement' => Zend_Form_Decorator_Abstract::PREPEND
        ));
        $this->_userpic->setDecorators(array(
            array('ViewHelper'),
            array(
                'decorator' => array('br' => 'HtmlTag'),
                'options' => array(
                    'tag' => 'br',
                    'placement' => Zend_Form_Decorator_Abstract::PREPEND
                )),
            $this->_userpicImageDecorator,
            array(
                'decorator' => array('td' => 'HtmlTag'),
                'options' => array('tag' => 'td')
            ),
            new App_Form_Decorator_Label(array(
                'tag' => 'td',
                'tagClass' => 'label bottom',
                'requiredSuffix' => '<b>*</b>:',
                'optionalSuffix' => ':'
            )),
            array(
                'decorator' => array('tr' => 'HtmlTag'),
                'options' => array('tag' => 'tr')
            ),
        ));

        // Submit
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
     * @see Zend_Form::setDefaults()
     *
     * @param array $defaults
     * @return Zend_Form
     */
    public function setDefaults(array $defaults)
    {
        if (isset($defaults['userpic'])) {
            $this->_userpicImageDecorator->setOption('src', $defaults['userpic']);

            unset($defaults['userpic']);
        }

        parent::setDefaults($defaults);
    }
}