<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 13/07/17
 * Time: 2:12 PM
 */

namespace AppBundle\Module\Configuration;


use Doctrine\Common\Collections\ArrayCollection;

class Filters
{
    /** @var $userTypes_available ArrayCollection */
    private static $userTypes_available;
    /** @var $userTypes_available ArrayCollection */
    private static $deviceTypes_available;
    /** @var $availabilityTypes_available ArrayCollection */
    private static $availabilityTypes_available;

    public $user_type = [];
    public $device_type = [];
    public $availability = [];
    public $search = [];
    public $date = [];

    public function __construct()
    {
        if( !isset(self::$userTypes_available) )
            self::$userTypes_available = new ArrayCollection(['standard', 'internal', 'premium']);

        if( !isset(self::$deviceTypes_available) )
            self::$deviceTypes_available = new ArrayCollection(['android', 'ios']);

        if( !isset(self::$availabilityTypes_available) )
            self::$availabilityTypes_available = new ArrayCollection(['orange hat', 'regular']);
    }

    /**
     * @return string[]
     */
    public function getUserType(): array
    {
        return $this->user_type;
    }

    /**
     * @param array $types
     * @throws \Exception
     */
    public function setUserType(array $types = null)
    {
        $this->user_type = null;
        if( $types != null )
            foreach ($types as $type)
                $this->addUserType($type);
    }

    /**
     * @param string $type
     * @throws \Exception
     */
    public function addUserType(string $type)
    {
        if( !self::$userTypes_available->contains(strtolower($type)))
            throw new \Exception("Bad module configuration: " . $type . " is not available as a user type.");

        $this->user_type[] = $type;
    }

    /**
     * @return string[]
     */
    public function getDeviceType(): array
    {
        return $this->device_type;
    }

    /**
     * @param array $types
     * @internal param array $array
     */
    public function setDeviceType(array $types = null)
    {
        $this->device_type = null;
        if( $types != null )
            foreach ($types as $type)
                $this->addDeviceType($type);
    }

    /**
     * @param string $type
     * @throws \Exception
     */
    public function addDeviceType(string $type)
    {
        if( !self::$deviceTypes_available->contains(strtolower($type)))
            throw new \Exception("Bad module configuration: " . $type . " is not available as a device type.");

        $this->device_type[] = $type;
    }

    /**
     * @param string $value
     * @throws \Exception
     */
    public function addAvailability(string $value)
    {
        if( !self::$availabilityTypes_available->contains(strtolower($value)))
            throw new \Exception("Bad module configuration: " . $value . " is not available as an availability type.");

        $this->availability[] = $value;
    }

    /**
     * @return string[]
     */
    public function getAvailability(): array
    {
        return $this->availability;
    }

    /**
     * @param array $types
     * @throws \Exception
     */
    public function setAvailability(array $types = null)
    {
        $this->availability = null;
        if( $types != null )
            foreach ($types as $type)
                $this->addAvailability($type);
    }

    /**
     * @param string $name
     * @param DateRange $date
     */
    public function addDate(string $name, DateRange $date)
    {
        $this->date[$name] = $date;
    }

    /**
     * @param string $name
     * @param Search $search
     */
    public function addSearch(string $name,Search $search)
    {
        $this->search[$name] = $search;
    }

    public function isEmpty(){
        if( $this->user_type == null &&
            $this->device_type == null &&
            $this->availability == null &&
            $this->search == null &&
            $this->date == null
        )
            return true;
        else
            return false;
    }

    /**
     * returns a json string
     * @return array
     */
    public function extract(): array
    {
        return ((array) $this);
    }
}