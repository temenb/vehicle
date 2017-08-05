<?php

/**
 * @interface IVehicle
 */
interface IVehicle {

    /**
     * Изменение силы давления на педаль скорости.
     *
     * @param $pressureUnits int Величина на которую изменится давление на педаль.
     *
     * @return void
     */
    public function changePedalPressure($pressureUnits);

    /**
     * Изменение положения руля.
     *
     * @param $pressureUnits int Угол на который будет изменено положение руля
     *
     * @return void
     */
    public function rotateWheel($angle);

    /**
     * Функция возвращающая текущую скорость.
     *
     * @return int
     */
    public function getSpeed();

    /**
     * Получить текущие координаты ТС.
     *
     * @return array
     */
    public function getCoordinates();

    /**
     * Получить текущее направление ТС.
     *
     * @return void
     */
    public function getDirection();
}