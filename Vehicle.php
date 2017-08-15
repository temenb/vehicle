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
    protected $key = 'DEFAULT_VEHICLE';

    /**
     * Конструктор.
     *
     * @return void
     */
    public function __construct($key = false)
    {
        if ($key) {
            $this->key = $key;
        }
        $this->restoreData();
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
     * Сохранить текущие характеристики ТС.
     *
     * @return void
     */
    public function save() {

        $dbLink = Db::getInstance()->getLink();
        mysqli_begin_transaction($dbLink);

        $stmt = mysqli_prepare(
            $dbLink,
            "INSERT INTO vehicle (`key`) VALUES (?)"
        );
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $this->key);
            /* запускаем запрос */
            mysqli_stmt_execute($stmt);
            /* закрываем запрос */
            mysqli_stmt_close($stmt);
        }

        $query = "INSERT INTO speed (v_id, `value`) VALUES (?,?)";
        $stmt = mysqli_prepare($dbLink, $query);
        if ($stmt) {
            $speed = $this->getSpeed();
            $insertId = $dbLink->insert_id;
            mysqli_stmt_bind_param($stmt, "ii", $insertId, $speed);
            /* запускаем запрос */
            mysqli_stmt_execute($stmt);
            /* закрываем запрос */
            mysqli_stmt_close($stmt);
        }

    }

    /**
     * Восстановить текущие характеристики ТС.
     *
     * @return void
     */
    private function restoreData() {

        $dbLink = Db::getInstance()->getLink();
        $query = "select v.id vid, v.key `key`, t.value `time`, `c`.value_x c_x, `c`.value_y c_y,
d.value direction, s.value speed 
from vehicle v
join `time` t on t.v_id=v.id
join speed s on s.v_id=v.id
join coordinate `c` on `c`.v_id=v.id
join direction d on d.v_id=v.id
where v.key=?";

        if ($stmt = mysqli_prepare($dbLink, $query)) {
            mysqli_stmt_bind_param($stmt, "s", $this->key);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $vehicleData = mysqli_fetch_array($result, MYSQLI_ASSOC);
            if (!$vehicleData) {
                $newData = true;
                $vehicleData = array(
                    'speed' => 0,
                    'direction' => 0,
                    'time' => time(),
                    'c_y' => 0,
                    'c_x' => 0,
                );
            }
            $this->setSpeed($vehicleData['speed']);
            $this->setDirection($vehicleData['direction']);
            $this->setCoordinates(['x' => $vehicleData['c_x'], 'y' => $vehicleData['c_y']]);
            $this->time = $vehicleData['time'];
            mysqli_stmt_close($stmt);
            if (!empty($newData)) {
                $this->save();
            }
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