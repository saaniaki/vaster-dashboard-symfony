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
    public $layout;
    public $presentation;
    public $remove_zeros = true;

    public function __construct(ArrayCollection $data = null)
    {
        if( !isset(Configuration::$singleCategories) )
            Configuration::$singleCategories = new ArrayCollection(['user_type', 'device_type', 'availability']);

        if( $data != null )
            $this->load($data);
        else {
            $this->filters = new Filters();
            $this->categories = new Categories();
            $this->layout = new Layout();
            $this->presentation = new Presentation();
        }

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
     * @return Filters|null
     */
    public function getFilters(): ?Filters
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
     * @return Presentation
     */
    public function getPresentation(): ?Presentation
    {
        return $this->presentation;
    }

    /**
     * @param Presentation $presentation
     */
    public function setPresentation(Presentation $presentation = null)
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
     * @return Layout
     */
    public function getLayout(): Layout
    {
        return $this->layout;
    }

    /**
     * @param Layout $layout
     */
    public function setLayout(Layout $layout)
    {
        $this->layout = $layout;
    }

    /**
     * returns a json string
     * @return array
     */
    public function extract(): array
    {
        if( $this->getFilters() != null && $this->getFilters()->isEmpty() ){
            $other = clone $this;
            $other->setFilters();
            return ((array) $other);
        }

        return ((array) $this);
    }


    public function load(ArrayCollection $data)
    {
        $categories = $data->get('categories');
        $filters = $data->get('filters');
        $layout = $data->get('layout');
        $presentation = $data->get('presentation');
        //$removeZeros = $data->get('remove_zeros');

        ////////////////////////////////////////////////////////////////////////// Filters: creating $filtersObj

        if( $filters != null ){
            $filtersObj = new Filters();
            if( isset($filters['user_type']) && $filters['user_type'] != null )$filtersObj->setUserType($filters['user_type']);
            if( isset($filters['availability']) && $filters['availability'] != null )$filtersObj->setAvailability($filters['availability']);
            if( isset($filters['device_type']) && $filters['device_type'] != null )$filtersObj->setDeviceType($filters['device_type']);

            if( isset($filters['search']) ){
                foreach ( $filters['search'] as $name => $SearchArray ){
                    $search = new Search();
                    $search->setKeyword($SearchArray['keyword']);
                    $search->setColumnOperator($SearchArray['columnOperator']);
                    $search->setExpressionOperator($SearchArray['expressionOperator']);
                    $search->setColumns($SearchArray['columns']);
                    $search->setNegate($SearchArray['negate'] === true? true: false);
                    $filtersObj->addSearch($name, $search);
                }
            }

            if( isset($filters['date']) ){
                foreach ( $filters['date'] as $name => $rangeArray ){
                    $range = new DateRange();
                    $range->setFrom($rangeArray['from']);
                    $range->setTo($rangeArray['to']);
                    $range->setColumn($rangeArray['column']);
                    $range->setOperator($rangeArray['operator']);
                    $range->setNegate($rangeArray['negate'] === true? true: false);
                    $filtersObj->addDate($name, $range);
                }
            }

        }

        ////////////////////////////////////////////////////////////////////////// Categories: creating $categoriesObj

        if( $categories != null ){
            $categoriesObj = new Categories();
            if( isset($categories['single']) && $categories['single'] != null )$categoriesObj->setSingle($categories['single']);

            if( isset($categories['multi']['search']) ) {
                foreach ($categories['multi']['search'] as $name => $SearchArray) {
                    $search = new Search();
                    $search->setKeyword($SearchArray['keyword']);
                    $search->setColumnOperator($SearchArray['columnOperator']);
                    $search->setExpressionOperator($SearchArray['expressionOperator']);
                    $search->setColumns($SearchArray['columns']);
                    $search->setNegate($SearchArray['negate'] === true? true: false);
                    $categoriesObj->addSearch($name, $search);
                }
            }

            if( isset($categories['multi']['date']) ) {
                foreach ($categories['multi']['date'] as $name => $rangeArray) {
                    $range = new DateRange();
                    $range->setFrom($rangeArray['from']);
                    $range->setTo($rangeArray['to']);
                    $range->setColumn($rangeArray['column']);
                    $range->setOperator($rangeArray['operator']);
                    $range->setNegate($rangeArray['negate'] === true? true: false);
                    $categoriesObj->addDate($name, $range);
                }
            }
        }

        ////////////////////////////////////////////////////////////////////////// Layout: creating $layoutObj

        if( $layout != null ){
            $layoutObj = new Layout();
            if( isset($layout['title']) && $layout['title'] != null )$layoutObj->setTitle($layout['title']);
            if( isset($layout['size']) && $layout['size'] != null )$layoutObj->setSize($layout['size']);
        }

        ////////////////////////////////////////////////////////////////////////// Presentation: creating $presentationObj

        if( $presentation != null ){
            $presentationObj = new Presentation();
            if( isset($presentation['data']) && $presentation['data'] != null )$presentationObj->setData($presentation['data']);
            if( isset($presentation['interval']) && $presentation['interval'] != null )$presentationObj->setInterval($presentation['interval']);
            if( isset($presentation['zero'])){$presentationObj->setZero($presentation['zero']);}
        }

        ////////////////////////////////////////////////////////////////////////// Configuration: setting up $configuration
        //if( isset($removeZeros) && $removeZeros != null )           $this->setRemoveZeros($removeZeros);
        if( isset($filtersObj) && $filtersObj != null )             $this->setFilters($filtersObj);
        if( isset($categoriesObj) && $categoriesObj != null )       $this->setCategories($categoriesObj);
        if( isset($layoutObj) && $layoutObj != null )               $this->setLayout($layoutObj);
        if( isset($presentationObj) && $presentationObj != null )   $this->setPresentation($presentationObj);

    }

}