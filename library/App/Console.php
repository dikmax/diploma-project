<?php
/**
 * Books social network
 *
 * LICENSE: Closed source
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id$
 */

require_once 'App/Console/Controller.php';
require_once 'Zend/Console/Getopt.php';

/**
 * Object for controll console actions
 *
 * @copyright  2008 Maxim Dikun
 * @version    Release: 0.0.1
 */
class App_Console
{
    /**
     * Console controller
     *
     * @var App_Console_Controller
     */
    protected $_controller = null;

    /**
     * Constructs main console controller class
     */
    public function __construct()
    {
        $this->showCopyright();
    }

    /**
     * Shows copyright notice
     */
    private function showCopyright()
    {
        echo "Console processing component\n\n";
    }

    /**
     * Process request parameters
     */
    public function process()
    {
        try {
            $controller = $this->getController();

            $options = $controller->getOptionsList();
            $options['help|h'] = 'Shows help';

            $opts = new Zend_Console_Getopt($options);
            $opts->parse();

            if ($opts->getOption('help')) {
                echo $opts->getUsageMessage();
                return;
            }

            $this->processOptions($opts->getOptions());
        } catch (Zend_Console_Getopt_Exception $e) {
            echo "Exception: " . $e->getMessage() . "\n";
        }
    }

    public function processOptions(array $options)
    {
        $controller = $this->getController();

        foreach ($options as $option) {
            if ($option !== 'help') {
                var_dump($option);
                $controller->executeAction($option);
            }
        }
    }

    /**
     * @return App_Console_Controller
     */
    public function getController()
    {
        if ($this->_controller === null) {
            $this->_controller = new App_Console_Controller();
        }
        return $this->_controller;
    }

    /**
     * @param App_Console_Controller $controller
     */
    public function setController(App_Console_Controller $controller)
    {
        $this->_controller = $controller;
    }
}
