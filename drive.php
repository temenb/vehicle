<?php
/**
 * @file  Сценарий управления транспортным средством посредством ajax запроса.
 */

spl_autoload_register(function($class) {
   require_once str_replace('_', DIRECTORY_SEPARATOR, $class . '.php');
});

session_start();
$car = new Car();
if (isset($_POST['rotate_right'])) {
    $car->rotateWheel($_POST['rotate_amount']);
} elseif (isset($_POST['rotate_left'])) {
    $car->rotateWheel(-$_POST['rotate_amount']);
}
if (isset($_POST['pedal_pressure_up'])) {
    $car->changePedalPressure($_POST['pedal_pressure_amount']);
} elseif (isset($_POST['pedal_pressure_down'])) {
    $car->changePedalPressure(-$_POST['pedal_pressure_amount']);
}

header('Content-Type: application/json');
echo json_encode('success');