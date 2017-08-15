<?php

/**
 * @class Car
 *
 */
class Car extends Vehicle {
    /**
     * @var int Коэфициент преобразования давления на педали в скорость движения.
     */
    protected $pedalSpeedKoef = 10;
    /**
     * @var int Коэфициент преобразования поворота руля в угол повотора транспортного средства.
     */
    protected $wheelDirectionKoef = 1;
    /**
     * @var int Ключ ячейки сессии, где хранится ключ сессии для сохранения состояния транспортного средства.
     */
    protected $key = 'DEFAULT_CAR';
}