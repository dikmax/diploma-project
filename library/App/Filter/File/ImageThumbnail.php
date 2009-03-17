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
 * @see Zend_Filter_Interface
 */
require_once 'Zend/Filter/Interface.php';

/**
 * model
 */
class App_Filter_File_ImageThumbnail implements Zend_Filter_Interface
{
    public function __contruct()
    {
        /*
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        } elseif (!is_array($options)) {
            require_once 'Zend/Filter/Exception.php';
            throw new Zend_Filter_Exception('Invalid options argument provided to filter');
        }
        */
    }

    /**
     * Defined by Zend_Filter_Interface
     *
     * Creates thumbnail from file
     * Returns the file $value, removing all but digit characters
     *
     * @param  string $value Full path of file to change
     * @throws Zend_Filter_Exception
     * @return string The new filename which has been set, or false when there were errors
     */
    public function filter($value)
    {
        if (!file_exists($value)) {
            require_once 'Zend/Filter/Exception.php';
            throw new Zend_Filter_Exception("File '$value' not found");
        }

        if (!is_writable($value)) {
            require_once 'Zend/Filter/Exception.php';
            throw new Zend_Filter_Exception("File '$value' is not writable");
        }

        // TODO write image processing classes

        $thumbnail = new Thumbnail(file_get_contents($value));
        $thumbnail->resize(100, 100);
        $thumbnail->save($value . ".jpg");

        return $value . ".jpg";
    }
}
