<?php
interface IVehicle {
    public function changePedalPressure($pressureUnits);
    public function rotateWheel($angle);
    public function getSpeed();
    public function getCoordinates();
    public function getDirection();
}