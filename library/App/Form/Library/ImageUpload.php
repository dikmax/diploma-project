<?php
/**
 * Books social network
 *
 * LICENSE: Closed source
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id:$
 */

require_once 'App/Form/Table.php';
require_once 'Zend/Filter/File/Rename.php';
require_once 'Zend/Form/Decorator/HtmlTag.php';
require_once 'Zend/Form/Element/File.php';
require_once 'Zend/Form/Element/Submit.php';
require_once 'Zend/Validate/File/Extension.php';
require_once 'Zend/Validate/File/IsImage.php';
require_once 'Zend/Validate/File/Size.php';

class App_Form_Library_ImageUpload extends App_Form_Table
{
    /**
     * @var Zend_Form_Element_File
     */
    protected $_uploadImage;

    /**
     * @var string
     */
    protected $_temporaryFileName;

    /**
     * @var Zend_Form_Decorator_HtmlTag
     */
    protected $_userpicImageDecorator;

    /**
     * @var Zend_Form_Element_Submit
     */
    protected $_submit;

    /**
     * Delete temporary files
     */
    public function __destruct()
    {
        if (file_exists($this->_temporaryFileName)) {
            unlink($this->_temporaryFileName);
        }
    }

    protected function initForm()
    {
        $this->setMethod('post')
             ->setAttrib('enctype', 'multipart/form-data')
             ->setNoStarOnRequired(true);
    }

    protected function initElements()
    {
        $sessionUser = App_User_Factory::getSessionUser();
        if ($sessionUser === null) {
            throw new App_Exception('Not logged in');
        }

        $this->_temporaryFileName = tempnam(Zend_Registry::get('tempPath') , 'libraryimage');
        $this->_uploadImage = new Zend_Form_Element_File('upload_image', array(
            'required' => true,
            'label' => 'Картинка'
        ));
        $this->_uploadImage->addValidators(array(
            new Zend_Validate_File_IsImage(),
            new Zend_Validate_File_Extension(array('jpg', 'png')),
            new Zend_Validate_File_Size(1048576) // 1M
        ));
        $this->_uploadImage->addFilters(array(
            new Zend_Filter_File_Rename(array(
                'target' => $this->_temporaryFileName,
                'overwrite' => true
            ))
        ));

        $this->_submit = new Zend_Form_Element_Submit('submit', array(
            'label' => 'Загрузить',
        ));

        $this->addElements(array(
            $this->_uploadImage,
            $this->_submit
        ));
    }

    /**
     * @see App_Form_Table::initCustomLayout()
     */
    protected function initCustomLayout()
    {
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
}