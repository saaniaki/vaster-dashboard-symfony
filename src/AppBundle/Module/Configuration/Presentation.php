<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 05/08/17
 * Time: 1:14 PM
 */

namespace AppBundle\Module\Configuration;


class Presentation
{
    public $data = 'User';
    public $field = 'Created Time 2';
    public $interval = 'Daily';
    public $snapShots = [];
    public $zero = false;

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @param string $field
     */
    public function setField(string $field)
    {
        $this->field = $field;
    }

    /**
     * @return string
     */
    public function getData() :string
    {
        return $this->data;
    }

    /**
     * @param string $data
     */
    public function setData(string $data)
    {
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getInterval() :string
    {
        return $this->interval;
    }

    /**
     * @param string $interval
     */
    public function setInterval(string $interval)
    {
        $this->interval = $interval;
    }

    /**
     * @return array
     */
    public function getSnapShots(): array
    {
        return $this->snapShots;
    }

    /**
     * @param String $name
     * @param String $date
     * @internal param array $snapShots
     */
    public function addSnapShots(String $name, String $date)
    {
        $this->snapShots[$name] = $date;
    }

    /**
     * @param array $snapShots
     */
    public function setSnapShots(array $snapShots)
    {
        $this->snapShots = $snapShots;
    }

    /**
     * @return bool
     */
    public function isZero(): bool
    {
        return $this->zero;
    }

    /**
     * @param $zero
     */
    public function setZero($zero)
    {
        if($zero === "false") $zero = false;
        $this->zero = $zero;
    }
}