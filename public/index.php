<?php
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */

ini_set('memory_limit', '-1');

chdir(dirname(__DIR__));

// Setup autoloading
require 'init_autoloader.php';

// Run the application!
Zend\Mvc\Application::init(require 'config/application.config.php')->run();
