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
    public $data = 'Registration';
    public $interval = 'Daily';
    public $zero = false;

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