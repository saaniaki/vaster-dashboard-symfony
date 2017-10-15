<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 28/07/17
 * Time: 8:45 PM
 */

namespace AppBundle\Module;


use AppBundle\Module\Configuration\DateRange;
use AppBundle\Module\Configuration\Search;

class Combination
{
    private $name;

    private $user_type;
    private $device_type;
    private $availability;
    private $search;
    private $date;
    private $snapShot = null; //string

    public function __construct($combination)
    {
        foreach( $combination as $key => $cat ){
            if( is_string($cat) ) {

                $this->name .= "/" . $cat;

                if( strtolower($key) == 'user_type' ) $this->user_type = $cat;

                if( strtolower($key) == 'availability' ){
                    if( strtolower($cat) == "orange hat" ) $this->availability = true;
                    else $this->availability = false;
                }
                if( strtolower($key) == 'device_type' ){
                    if( strtolower($cat) == "android" ) $this->device_type = ["Android"];
                    else if( strtolower($cat) == "ios" ) $this->device_type = ["iPhone", "iPad"];
                }

            }
            else {
                if( $cat->isNegate() ) $this->name .= "/!" . $key;
                else $this->name .= "/" . $key;

                if( $this->get_class_name(get_class($cat)) == 'Search' ) $this->search[] = $cat;
                if( $this->get_class_name(get_class($cat)) == 'DateRange' ) $this->date[] = $cat;
            }
        }
        $this->name = substr($this->name, 1);
    }

    private function get_class_name($className)
    {
        if ($pos = strrpos($className, '\\')) return substr($className, $pos + 1);
        return $pos;
    }

    /**
     * @param bool|string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return null|string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getUserType()
    {
        return $this->user_type;
    }

    /**
     * @return array|null
     */
    public function getDeviceType()
    {
        return $this->device_type;
    }

    /**
     * @return bool|null
     */
    public function isAvailability()
    {
        return $this->availability;
    }

    /**
     * @return mixed
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }


    /**
     * @return bool
     */
    public function isSnapShot(): bool
    {
        return $this->snapShot !== null;
    }

    /**
     * @return string
     */
    public function getSnapShot(): string
    {
        return $this->snapShot;
    }

    /**
     * @param string $snapShot
     */
    public function setSnapShot(string $snapShot = null)
    {
        $this->snapShot = $snapShot;
    }
}