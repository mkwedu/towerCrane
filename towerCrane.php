<?php

class TowerCrane
{
    public $name;
    public $towerHeight;
    public $armRange;
    public $liftingCapacity;
    
    private $on = false;
    private $powerConsumption = 0;
    private $armAngle = 0;
    private $trolleyPosition = 0;
    private $loadHeight = 0;

    /**
     * Load the state of the tower crane
     */
    public function __construct()
    {
        if(file_exists('towerCraneState.txt')) {
            $array = unserialize(file_get_contents('towerCraneState.txt'));
            foreach($array as $key => $value) {
                $this->$key = $value;
            }
        }
    }

    /**
     * Save the state of the tower crane
     * @return json
     */
    public function __destruct()
    {
        $array = [
            'name' => $this->name,
            'on' => $this->on,
            'towerHeight' => $this->towerHeight,
            'armRange' => $this->armRange,
            'liftingCapacity' => $this->liftingCapacity,
            'powerConsumption' => $this->powerConsumption,
            'armAngle' => $this->armAngle,
            'trolleyPosition' => $this->trolleyPosition,
            'loadHeight' => $this->loadHeight
        ];
        file_put_contents('towerCraneState.txt', serialize($array));
    }

    public function __get($name) {
        return $this->$name;
    }

    public function __set($name, $value) {
        $this->$name = $value;
    }

    /**
     * Turn on the tower crane
     * @return bool
     */
    public function turnOn()
    {
        $this->on = true;
        $this->powerConsumption = 100;
        return $this->on;
    }

    /**
     * Turn off the tower crane
     * @return bool
     */
    public function turnOff()
    {
        $this->on = false;
        $this->powerConsumption = 0;
        return $this->on;
    }

    /**
     * Rotate the arm of the tower crane
     * @param $deg
     */
    private function rotateArm($deg)
    {
        if($this->armAngle + $deg < 360) {
            $this->armAngle += $deg;
        } else {
            $this->armAngle += $deg - 360;
        }
    }

    /**
     * Move the trolley
     * @param $px
     */
    private function moveTrolley($px)
    {
        if($this->trolleyPosition + $px > $this->armRange) {
            $this->trolleyPosition = $this->armRange;
        } elseif ($this->trolleyPosition + $px < 0) {
            $this->trolleyPosition = 0;
        } else {
            $this->trolleyPosition += $px;
        }
    }

    /**
     * Lift the load
     * @param $px
     */
    private function liftLoad($px)
    {
        $this->loadHeight += $px;
    }

    /**
     * Get the state of the tower crane
     * @return json
     */
    public function getState()
    {
        $array = [
            'name' => $this->name,
            'on' => $this->on,
            'towerHeight' => $this->towerHeight,
            'armRange' => $this->armRange,
            'liftingCapacity' => $this->liftingCapacity,
            'powerConsumption' => $this->powerConsumption,
            'armAngle' => $this->armAngle,
            'trolleyPosition' => $this->trolleyPosition,
            'loadHeight' => $this->loadHeight
        ];
        return json_encode($array);
    }

    /**
     * Control the tower crane
     * @param $command - options: left, right, forward, backward, up, down
     * @return json
     */
    public function control($command)
    {
        if (!$this->on) {
            return "{'error': 'The tower crane is off'}";
        }
        
        // Perform the command
        switch ($command) {
            case 'left':
                $this->rotateArm(-5);
                break;
            case 'right':
                $this->rotateArm(5);
                break;
            case 'forward':
                $this->moveTrolley(3);
                break;
            case 'backward':
                $this->moveTrolley(-3);
                break;
            case 'up':
                $this->liftLoad(1);
                break;
            case 'down':
                $this->lowerLoad(-1);
                break;
            default:
                return "{'error': 'Invalid command'}";
                break;
        }
        return $this->getState();
    }
}

// $crane = new TowerCrane();
// $crane->name = 'Tower Crane 1';
// $crane->towerHeight = 100;
// $crane->armRange = 50;
// $crane->liftingCapacity = 500;

// $crane->turnOn();

// $crane->turnOff();
// // __destruct