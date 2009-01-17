#!/usr/bin/php
<?php
/**
 * Books social network
 *
 * @copyright  2008 Dikun Maxim
 * @version    $Id$
 */

//ini_set('memory_limit', '3072M');
set_time_limit(300);

require_once dirname(__FILE__) . '/bootstrap.php';

$console = new App_Console();
$console->getController()->setControllersFolder(dirname(__FILE__) . '/controllers');
$console->process();

// Debug info
echo 'Memory usage: ' . memory_get_usage(true) . " bytes\n";

/*
$instancesCounts = App_Debug_InstancesCounter::getInstancesCounts();
foreach($instancesCounts as $class => $count) {
    echo $class . ': ' . $count . "\n";
}
*/