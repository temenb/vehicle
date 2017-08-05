<?php
/**
 * @file Сценарий получения текущих характеристик ТС посредством ajax запроса.
 */

spl_autoload_register(function($class) {
   require_once str_replace('_', DIRECTORY_SEPARATOR, $class . '.php');
});

session_start();
$car = new Car();
$response = array(
    'direction' => $car->getDirection(),
    'speed' => $car->getSpeed(),
    'coordinates' => $car->getCoordinates(),
);

header('Content-Type: application/json');
echo json_encode($response);