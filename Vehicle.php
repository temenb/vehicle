<?php
abstract class Vehicle implements IVehicle {
    protected $speed = 0;
    protected $coordinates = array('x' => 0, 'y' => 0);
    protected $direction = 0;
    protected $time;

    public function __construct($sessionKey = false)
    {
        if ($sessionKey) {
            $this->restoreFromSession($sessionKey);
        } else {
            $this->time = time();
        }
    }

    public function getSpeed()
    {
        return $this->speed;
    }

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
        $this->recalculateCoordinates();
        return $this->_getCoordinates();
    }

    private function _getCoordinates()
    {
        return $this->coordinates;
    }

    private function setCoordinates($coordinates)
    {
        $this->coordinates = $coordinates;
    }

    public function getDirection()
    {
        return $this->direction;
    }

    private function setDirection($direction)
    {
        $this->recalculateCoordinates();
        $direction = $direction%360;
        $this->direction = $direction;
    }

    private function recalculateCoordinates() {
        $coordinates = $this->_getCoordinates();
        $coordinates['x'] =
            $coordinates['x'] + $this->getSpeed()*(time()-$this->time)
            *cos(pi()*$this->getDirection()/180);
        $coordinates['y'] =
            $coordinates['y'] + $this->getSpeed()*(time()-$this->time)
            *sin(pi()*$this->getDirection()/180);
        $this->setCoordinates($coordinates);
        $this->time = time();
    }

    public function changePedalPressure($pressureUnits)
    {
        $this->setSpeed($this->getSpeed() + $pressureUnits*$this->pedalSpeedKoef);
    }

    public function rotateWheel($angle)
    {
        $this->setDirection($this->getDirection() + $angle*$this->wheelDirectionKoef);
    }

    public function saveToSession($key) {
        $_SESSION[$key]['speed'] = $this->getSpeed();
        $_SESSION[$key]['direction'] = $this->getDirection();
        $_SESSION[$key]['coordinates'] = $this->_getCoordinates();
        $_SESSION[$key]['time'] = $this->time;
    }

    private function restoreFromSession($key) {
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
}