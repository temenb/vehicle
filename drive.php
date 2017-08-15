<?php
/**
 * @file  Сценарий управления транспортным средством посредством ajax запроса.
 */

spl_autoload_register(function($class) {
   require_once str_replace('_', DIRECTORY_SEPARATOR, $class . '.php');
});

$car = new Car();
if ($_POST['action']=='rotate_right') {
    $car->rotateWheel($_POST['rotate_amount']);
} elseif ($_POST['action']=='rotate_left') {
    $car->rotateWheel(-$_POST['rotate_amount']);
}
if ($_POST['action']=='pedal_pressure_up') {
    $car->changePedalPressure($_POST['pedal_pressure_amount']);
} elseif ($_POST['action']=='pedal_pressure_down') {
    $car->changePedalPressure(-$_POST['pedal_pressure_amount']);
}

header('Content-Type: application/json');
echo json_encode('success');