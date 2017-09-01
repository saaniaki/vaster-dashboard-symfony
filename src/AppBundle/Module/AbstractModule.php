<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 28/07/17
 * Time: 5:49 PM
 */

namespace AppBundle\Module;


use AppBundle\Entity\Module;
use AppBundle\Module\Configuration\Configuration;
use AppBundle\Module\Configuration\Filters;
use AppBundle\Module\Configuration\Presentation;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ManagerRegistry;

abstract class AbstractModule implements ModuleInterface
{
    /**
     * @var Module
     */
    protected $module;



    protected $title;
    protected $footer;
    public $color;
    protected $type;
    protected $size;

    protected $tooltip_shared = 1; //true
    protected $currentlyShowing = 0;
    protected $all_data = [];

    protected $xTitle;
    protected $xAxisType; //linear, logarithmic, datetime or category
    protected $start = [
        'year' => 2016,
        'month' => 11,
        'day' => 9,
    ];


    public $yTitle;
    protected $yAllowDecimals;


    public $y1Title;
    protected $y1Format;
    protected $y1AllowDecimals;
    /** @var  $xInterval integer */
    protected $xInterval;

    /** @var $subModule SubModuleInterface */
    protected $subModule;

    public function __construct(Module $module, ManagerRegistry $managerRegistry)
    {
        $this->module = $module;
        $em = $managerRegistry->getManager('vaster');
        $this->color = new ArrayCollection(['#2f7ed8', '#0d233a', '#8bbc21', '#910000', '#1aadce', '#492970', '#f28f43', '#77a1e5', '#c42525', '#a6c96a']);


        ////////////////////////
        $available_data_sources = $this->module->getModuleInfo()->getAvailableConfiguration()["presentation"]["data"];
        $data_source = $module->getConfiguration()->getPresentation()->getData();


        //dump($available_data_sources, $data_source);die();

        if(  in_array($data_source, $available_data_sources) )
        {
            $data_source = "AppBundle\Module\Graph\SubModule\\" . $data_source;
            $this->subModule = new $data_source($this, $em);
        }

        else die('Data source does not exists!');
    }

    /**
     * @return array
     */
    public function render()
    {
        $configuration = $this->module->getConfiguration();
        $presentation = $configuration->getPresentation(); //this value should be valued!!
        $filters = $configuration->getFilters();
        $removeZeros = $configuration->isRemoveZeros();

        /*
         * getting all the possible categories
         */
        $categories = [];
        $singleCategories = $configuration->getCategories()->getSingle();
        $searchCategories = $configuration->getCategories()->getSearches();
        $dateCategories = $configuration->getCategories()->getDates();

        $improved_combinations = [];

        foreach ($singleCategories as $cat){
            $categories[strtolower($cat)] = $this->module->getModuleInfo()->getAvailableConfiguration()['filters'][strtolower($cat)];//get actual values from module info
        }

        foreach ($searchCategories as $catName => $value){
            //$categories[$catName][] = $value;
            //$value = clone $value;
            //$value->setNegate(!$value->isNegate());
            //$categories[$catName][] = $value;
            $temp = $categories;
            $temp[$catName][] = $value;
            foreach ($this->combinations($temp) as $combo)
                $improved_combinations[] = $combo;
        }


        foreach ($dateCategories as $catName => $value){
            //$categories[$catName][] = $value;
            //$value = clone $value;
            //$value->setNegate(!$value->isNegate());
            //$categories[$catName][] = $value;
            $temp = $categories;
            $temp[$catName][] = $value;
            foreach ($this->combinations($temp) as $combo)
                $improved_combinations[] = $combo;
        }

        if( $improved_combinations == null ){
            $improved_combinations = $this->combinations($categories);
        }


        $this->feedData($presentation, $improved_combinations, $filters, $removeZeros);



        return get_object_vars($this);
    }

    protected function combinations($data){
        $combinations = [[]];
        $comKeys = array_keys($data);

        for ($count = 0; $count < count($comKeys); $count++) {
            $tmp = [];
            foreach ($combinations as $v1) {
                foreach ($data[$comKeys[$count]] as $v2) {
                    //clone $v2
                    $tmp[] = $v1 + [$comKeys[$count] => $v2];
                }
            }
            $combinations = $tmp;
        }

        // making combination object
        $combinationsObj = [];
        foreach($combinations as $combination){
            $combinationsObj[] = new Combination($combination);
        }

        return $combinationsObj;
    }

    public function setTitle($default){
        $title = $this->module->getConfiguration()->getLayout()->getTitle();
        if($title == null) $this->title = $default;
        else $this->title = $title;
    }

    //create a utility class and move this function there
    public function adjustDate($fromDate, $toDate){
        $yesterday = new \DateTime('2000-01-01');
        $aWeekAgo = new \DateTime('2000-01-07');
        $aMonthAgo = new \DateTime('2000-02-01');

        //dump($fromDate, $toDate);die();

        if( $fromDate == null ) $fromDate = new \DateTime('2016-12-09');
        else $fromDate = new \DateTime($fromDate);

        if( $fromDate == $yesterday ) $fromDate = new \DateTime('midnight yesterday');
        elseif ( $fromDate == $aWeekAgo ) $fromDate = new \DateTime('midnight last week');
        elseif ( $fromDate == $aMonthAgo ) $fromDate = new \DateTime('midnight last month');
        //elseif( $fromDate == null ) $fromDate = new \DateTime('2016-12-09');


        if( $toDate == null ) $toDate = new \DateTime('now');
        else $toDate = new \DateTime($toDate);

        if( $toDate == $yesterday ) $toDate = new \DateTime('midnight yesterday');
        elseif ( $toDate == $aWeekAgo ) $toDate = new \DateTime('midnight last week');
        elseif ( $toDate == $aMonthAgo ) $toDate = new \DateTime('midnight last month');
        //elseif( $toDate == null ) $toDate = new \DateTime('now');


        return ['from' => $fromDate, 'to' => $toDate];
    }

    /**
     * @param Presentation $presentation
     * @param $combinations
     * @param $filters Filters
     * @param $removeZeros
     * @return mixed
     */
    abstract protected function feedData($presentation, $combinations, $filters, $removeZeros);
}