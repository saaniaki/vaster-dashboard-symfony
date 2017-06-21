<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 2017-06-19
 * Time: 5:57 PM
 */

namespace VasterBundle\Entity\Geo\ValueObject;

class Point
{

    /**
     * @param float $latitude
     * @param float $longitude
     */
    public function __construct($latitude, $longitude)
    {
        $this->latitude  = $latitude;
        $this->longitude = $longitude;
    }

    /**
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }
}