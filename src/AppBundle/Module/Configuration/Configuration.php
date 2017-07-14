<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 13/07/17
 * Time: 1:44 PM
 */

namespace AppBundle\Module\Configuration;



use Doctrine\Common\Collections\ArrayCollection;

class Configuration
{
    /** @var $singleCategories ArrayCollection */
    private static $singleCategories;

    public $categories;
    public $filters;
    public $presentation;
    public $remove_zeros = true;

    public function __construct()
    {
        if( !isset(Configuration::$singleCategories) )
            Configuration::$singleCategories = new ArrayCollection(['user_type', 'device_type', 'availability']);

        $this->filters = new Filters();
        $this->categories = new Categories();
    }

    /**
     * @return Categories
     */
    public function getCategories(): Categories
    {
        return $this->categories;
    }

    /**
     * @param Categories $categories
     */
    public function setCategories(Categories $categories)
    {
        $this->categories = $categories;
    }

    //get single cats
    //get multi cats
    //get search cats
    //get date cats

    /*
     * @param array $categories
     * @throws \Exception

    public function setSingleCategories(array $categories)
    {
        foreach ($categories as &$cat){

            $cat = strtolower($cat);

            if( !Configuration::$singleCategories->contains($cat) )
                throw new \Exception("Bad module configuration: " . $cat . " is not available as a single category.");
        }

        $this->categories['single'] = $categories;
    }*/

    /**
     * @return Filters
     */
    public function getFilters(): Filters
    {
        return $this->filters;
    }

    /**
     * @param Filters $filters
     */
    public function setFilters(Filters $filters = null)
    {
        $this->filters = $filters;
    }

    /**
     * @return string
     */
    public function getPresentation(): string
    {
        return $this->presentation;
    }

    /**
     * @param string $presentation
     */
    public function setPresentation(string $presentation)
    {
        $this->presentation = $presentation;
    }

    /**
     * @return bool
     */
    public function isRemoveZeros(): bool
    {
        return $this->remove_zeros;
    }

    /**
     * @param bool $remove_zeros
     */
    public function setRemoveZeros(bool $remove_zeros)
    {
        $this->remove_zeros = $remove_zeros;
    }

    /**
     * returns a json string
     * @return array
     */
    public function extract(): array
    {
        if( $this->getFilters()->isEmpty() ){
            $other = clone $this;
            $other->setFilters();
            return ((array) $other);
        }

        return ((array) $this);
    }

}