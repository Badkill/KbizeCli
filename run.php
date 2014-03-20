<?php
/*
 * Run the application
 */
namespace KbizeCli;

use Skel\DependencyInjection\Application;
use KbizeCli\Console\Helper\AlternateTableHelper;
use KbizeCli\Console\Helper\TableWithRowTitleHelper;

// the autoloader
$loader = require __DIR__ . '/vendor/autoload.php';

// create application
$application = new Application(__NAMESPACE__);

$helperSet = $application->getHelperSet();
$helperSet->set(new AlternateTableHelper());
$helperSet->set(new TableWithRowTitleHelper());
$application->setHelperSet($helperSet);


// and run
$application->run();
