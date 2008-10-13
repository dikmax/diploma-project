#!/usr/bin/php
<?php
require_once 'bootstrap.php';

$console = new App_Console();
$console->getController()->setControllersFolder('./controllers');
$console->process();