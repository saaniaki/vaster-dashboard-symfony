<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 13/07/17
 * Time: 4:25 PM
 */

namespace AppBundle\Module\Configuration;


use Doctrine\Common\Collections\ArrayCollection;

class Categories
{
    /** @var $singleCategories_available ArrayCollection */
    private static $singleCategories_available;

    public $single = [];
    public $multi = ['search' => [], 'date' => []];


    public function __construct()
    {
        if( !isset(self::$singleCategories_available) )
            self::$singleCategories_available = new ArrayCollection(['user_type', 'device_type', 'availability']);
    }

    /**
     * @param array $categories
     * @throws \Exception
     */
    public function setSingle(array $categories)
    {
        foreach ($categories as &$cat){

            $cat = strtolower($cat);

            if( !self::$singleCategories_available->contains($cat) )
                throw new \Exception("Bad module configuration: " . $cat . " is not available as a single category.");
        }

        $this->single = $categories;
    }

    //implement addSingle

    /**
     * @param string $name
     * @param DateRange $date
     */
    public function addDate(string $name, DateRange $date)
    {
        $this->multi['date'][$name] = $date;
    }

    /**
     * @return array
     */
    public function getSingle(): array
    {
        return $this->single;
    }

    /**
     * @return array
     */
    public function getMulti(): array
    {
        return $this->multi;
    }

    /**
     * @return array
     */
    public function getDates()
    {
        return $this->multi['date'];
    }

    /**
     * @return array
     */
    public function getSearches()
    {
        return $this->multi['search'];
    }

    /**
     * @param string $name
     * @param Search $search
     */
    public function addSearch(string $name, Search $search)
    {
        $this->multi['search'][$name] = $search;
    }
}