<?php
/**
 * @file Сценарий отображения транспортного средства
 */

spl_autoload_register(function($class) {
   require_once str_replace('_', DIRECTORY_SEPARATOR, $class . '.php');
});

session_start();
$car = new Car();

include_once 'index.phtml';