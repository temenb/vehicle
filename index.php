<?php
/**
 * @file Сценарий отображения транспортного средства
 */

spl_autoload_register(function($class) {
   require_once str_replace('_', DIRECTORY_SEPARATOR, $class . '.php');
});

$car = new Car();

include_once 'index.phtml';