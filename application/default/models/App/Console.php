<?php
/**
 * Books social network
 *
 * LICENSE: Closed source
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id:$
 */

/**
 * Object for controll console actions
 *
 * @copyright  2008 Maxim Dikun
 * @version    Release: 0.0.1
 */
class App_Console
{
    const OPTION_HELP = 'help';
    const OPTION_UPDATE_AUTHOR_NAME_INDEX = 'update_authors';

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
            $opts = new Zend_Console_Getopt(
                array(
                    self::OPTION_HELP => 'Shows help',
                    self::OPTION_UPDATE_AUTHOR_NAME_INDEX . '|a' => 'Update authors name index'
                )
            );
            $opts->parse();

            if ($opts->getOption('help')) {
                echo $opts->getUsageMessage();
                return;
            }


        } catch (Zend_Console_Getopt_Exception $e) {
            echo $e->getUsageMessage();
        }
    }

    public function processOptions(array $options)
    {
        foreach ($options as $option) {
            switch ($option) {
                case self::OPTION_HELP:
                    break;
                case self::OPTION_UPDATE_AUTHOR_NAME_INDEX:
                    break;
                default:
                    echo "Warning: undefined option ($option)\n";
                    break;
            }
        }
    }
}
