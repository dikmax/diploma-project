<?php
class App_Console_Controller
{
    /**
     * @var string
     */
    protected $_controllersFolder;

    /**
     * @var string
     */
    protected $_controllersClassPrefix;

    /**
     * @var array All controllers found in specified folder
     */
    protected $_registeredControllers;
    /**
     * Constructs console controller
     * @param array $options
     */
    public function __construct ($controllersDirectory = '', $controllersClassPrefix = '')
    {
        $this->_controllersFolder = $controllersDirectory;
        $this->_controllersClassPrefix = $controllersClassPrefix;

        $this->_registeredControllers = null;
    }

    /**
     * Scan folder and registers all controllers
     */
    private function scanControllersFolder()
    {
        $this->_registeredControllers = array();

        $dir = new DirectoryIterator($this->_controllersFolder);
    }

}
?>