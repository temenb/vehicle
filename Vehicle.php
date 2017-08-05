<?php

/**
 * @class Car
 *
 */
abstract class Vehicle implements IVehicle {

    /**
     * @var int Текущая скорость транспортного средства.
     */
    protected $speed = 0;

    /**
     * @var array Текущие координаты транспортного средства.
     */
    protected $coordinates = array('x' => 0, 'y' => 0);

    /**
     * @var int Текущий угол движения транспортного средства.
     */
    protected $direction = 0;

    /**
     * @var int Время последнего изменения характеристик движения транспортного средства.
     */
    protected $time;

    /**
     * @var int Ключ ячейки сессии, где хранится ключ сессии для сохранения состояния транспортного средства.
     */
    protected $sessionKey = 'DEFAULT_VEHICLE';

    /**
     * Конструктор.
     *
     * @return void
     */
    public function __construct($sessionKey = false)
    {
        if ($sessionKey) {
            $this->sessionKey = $sessionKey;
            $this->restoreFromSession();
        } elseif (empty($_SESSION[$this->sessionKey])) {
            $_SESSION[$this->sessionKey] = uniqid();
            $this->time = time();
        } else {
            $this->restoreFromSession();
        }
    }

    /**
     * Деструктор.
     *
     * @return void
     */
    public function __destruct()
    {
        $this->saveToSession();
    }

    /**
     * Функция возвращающая текущую скорость.
     *
     * @return int
     */
    public function getSpeed()
    {
        return $this->speed;
    }

    /**
     * Функция устанавливающая новуютекущую скорость.
     *
     * @param $speed int Новая скорость.
     *
     * @return int
     */
    private function setSpeed($speed)
    {
        $this->recalculateCoordinates();
        if ($speed < 0) {
            $speed = 0;
        }
        $this->speed = $speed;
    }

    public function getCoordinates()
    {
        $coordinates = $this->_getCoordinates();
        $coordinates['x'] =
            $coordinates['x'] + $this->getSpeed()*(time()-$this->time)
            *cos($this->convertAngleToRadians($this->getDirection()));
        $coordinates['y'] =
            $coordinates['y'] + $this->getSpeed()*(time()-$this->time)
            *sin($this->convertAngleToRadians($this->getDirection()));
        return $coordinates;
    }
    /**
     * Получить координаты ТС на момент изменения параметров движения.
     *
     * @return array
     */
    private function _getCoordinates()
    {
        return $this->coordinates;
    }
    /**
     * Получить текущие координаты ТС.
     *
     * @param $coordinates array Новые координаты транспортного средства.
     *
     * @return array
     */
    private function setCoordinates($coordinates)
    {
        $this->coordinates = $coordinates;
    }

    /**
     * Получить текущее направление ТС.
     *
     * @return void
     */
    public function getDirection()
    {
        return $this->direction;
    }

    /**
     * Изменить направление ТС.
     *
     * @param $direction int Направление транспортного средства в градусах.
     *
     * @return void
     */
    private function setDirection($direction)
    {
        $this->recalculateCoordinates();
        $direction = $direction%360;
        $this->direction = $direction;
    }

    /**
     * Пересчет текущих координат в момент изменения характеристик движения.
     *
     * @return void
     */
    private function recalculateCoordinates() {
        $this->setCoordinates($this->getCoordinates());
        $this->time = time();
    }

    /**
     * Изменение силы давления на педаль скорости.
     *
     * @param $pressureUnits int Величина на которую изменится давление на педаль.
     *
     * @return void
     */
    public function changePedalPressure($pressureUnits)
    {
        $this->setSpeed($this->getSpeed() + $pressureUnits*$this->pedalSpeedKoef);
    }

    /**
     * Изменение положения руля.
     *
     * @param $pressureUnits int Угол на который будет изменено положение руля.
     *
     * @return void
     */
    public function rotateWheel($angle)
    {
        $this->setDirection($this->getDirection() + $angle*$this->wheelDirectionKoef);
    }


    /**
     * Сохранить текущие характеристики ТС в сессию.
     *
     * @return void
     */
    public function saveToSession() {
        $key = $_SESSION[$this->sessionKey];
        $_SESSION[$key]['speed'] = $this->getSpeed();
        $_SESSION[$key]['direction'] = $this->getDirection();
        $_SESSION[$key]['coordinates'] = $this->_getCoordinates();
        $_SESSION[$key]['time'] = $this->time;
    }

    /**
     * Восстановить текущие характеристики ТС из сессии.
     *
     * @return void
     */
    private function restoreFromSession() {
        $key = $_SESSION[$this->sessionKey];
        if (isset($_SESSION[$key]['speed'])) {
            $this->setSpeed($_SESSION[$key]['speed']);
        }
        if (isset($_SESSION[$key]['direction'])) {
            $this->setDirection($_SESSION[$key]['direction']);
        }
        if (isset($_SESSION[$key]['coordinates'])) {
            $this->setCoordinates($_SESSION[$key]['coordinates']);
        }
        if (isset($_SESSION[$key]['time'])) {
            $this->time = $_SESSION[$key]['time'];
        }
    }

    /**
     * Преобразовать угол из градусов в радианы.
     *
     * @return int
     */
    private function convertAngleToRadians($angle)
    {
        return pi() * $angle / 180;
    }
}