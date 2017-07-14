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
     * @param DateRange[] $date
     */
    public function addDate(string $name, array $date)
    {
        //check is $date is and array of DateRanges (and make sure the last one's operator is null)
        $this->multi['date'][$name] = $date;
    }

    /**
     * @param string $name
     * @param Search[] $search
     */
    public function addSearch(string $name, array $search)
    {
        //check is $date is and array of Searches (and make sure the last one's operator is null)
        $this->multi['search'][$name] = $search;
    }
}