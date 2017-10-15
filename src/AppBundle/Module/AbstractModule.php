<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 28/07/17
 * Time: 5:49 PM
 */

namespace AppBundle\Module;


use AppBundle\Entity\Module;
use AppBundle\Library\ConditionTree;
use AppBundle\Library\SearchBinaryTree;
use AppBundle\Library\Stack;
use AppBundle\Module\Configuration\Configuration;
use AppBundle\Module\Configuration\Filters;
use AppBundle\Module\Configuration\Presentation;
use AppBundle\Module\Configuration\Settings;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ManagerRegistry;

abstract class AbstractModule implements ModuleInterface
{
    /** @var Settings */
    protected $configuration; //needs getter and setter
    /** @var \AppBundle\Entity\ModuleInfo */
    protected $moduleInfo; //needs getter and setter


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

    /**
     * AbstractModule constructor.
     * Parsing the database data to a VDP-Module Object
     * @param Module $module
     * @param ManagerRegistry $managerRegistry
     */
    public function __construct(Module $module, ManagerRegistry $managerRegistry)
    {
        //Setting up the main properties of a module
        //Grabbing the configuration JSON from the database, validate it, parse it, and storing the Configuration Object
        $this->configuration = $module->getConfiguration();
        //Grabbing the ModuleInfo data from the database and parse it to a ModuleInfo Object
        $this->moduleInfo = $module->getModuleInfo();

        //Setting up the default values which are the same fore all modules
        $this->color = new ArrayCollection(['#2f7ed8', '#0d233a', '#8bbc21', '#910000', '#1aadce', '#492970', '#f28f43', '#77a1e5', '#c42525', '#a6c96a']);

        //$em = $managerRegistry->getManager('vaster');
        //$available_data_sources = $this->graphType->getAvailableConfiguration()["presentation"]["data"];


        //This is the data source object which would be used to grab the data from the database
        $data_source = $this->configuration->getDataSource();
        //dump($data_source->initQuery());
        //xdebug_var_dump($data_source->initQuery());
        /*
            if(  in_array($data_source, $available_data_sources) )
            {
                $data_source = "AppBundle\Module\Graph\SubModule\\" . $data_source;
                $this->subModule = new $data_source($this, $em);
            }
            else die('Data source does not exists!');
        */


        dump($this->configuration->getFilters()->makeConditionTree());



        dump('not ready yet');die();
    }

    /**
     * @return array
     */
    public function render()
    {


        $presentation = $configuration->getPresentation();
        $filters = $configuration->getFilters();

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

        if( $improved_combinations == null ) $improved_combinations = $this->combinations($categories);

        /*
                // all categories are created by now
                // time to check if snapshots are requested, and add them as categories
                $snapShots = $configuration->getPresentation()->getSnapShots();
                $snapShotsCombinations = [];


                if( $snapShots != null ){
                    foreach ($snapShots as $name => $date){
                        foreach($improved_combinations as $combo  ){
                            /** @var Combination $newCombo *
                            $newCombo = clone $combo;
                            $newCombo->setName($name . " " . $newCombo->getName());
                            $newCombo->setSnapShot($date);
                            $snapShotsCombinations[] = $newCombo;
                        }
                    }
                }
                //import all snapshots
                foreach ($snapShotsCombinations as $combo) $improved_combinations[] = $combo;
        */



        $this->feedData($presentation, $improved_combinations, $filters);
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
        $NOW = new \DateTime('now');            // now time
        $BEG = new \DateTime('2016-12-09');     // beginning

        $yesterday = new \DateTime('2000-01-01');
        $aWeekAgo = new \DateTime('2000-01-07');
        $aMonthAgo = new \DateTime('2000-02-01');
        $threeMonthAgo = new \DateTime('2000-03-01');
        $oneYearAgo = new \DateTime('2001-01-01');

        //dump($fromDate, $toDate);die();

        if( $fromDate == null ) $fromDate = $BEG;
        else $fromDate = new \DateTime($fromDate);

        if( $fromDate == $yesterday ) $fromDate = (new \DateTime($NOW->format('Y-m-d H:00:00')))->modify('-23 hours');
        elseif ( $fromDate == $aWeekAgo ) $fromDate = (new \DateTime($NOW->format('Y-m-d 00:00:00')))->modify('-6 days');
        elseif ( $fromDate == $aMonthAgo ) $fromDate = (new \DateTime($NOW->format('Y-m-d 00:00:00')))->modify('-29 days');
        elseif ( $fromDate == $threeMonthAgo ) $fromDate = (new \DateTime($NOW->format('Y-m-d 00:00:00')))->modify('-89 days');
        elseif ( $fromDate == $oneYearAgo ) $fromDate = (new \DateTime($NOW->format('Y-m-d 00:00:00')))->modify('-364 days');
        //elseif( $fromDate == null ) $fromDate = new \DateTime('2016-12-09');


        if( $toDate == null ) $toDate = $NOW;
        else $toDate = new \DateTime($toDate);

        if( $toDate == $yesterday ) $toDate = (new \DateTime($NOW->format('Y-m-d H:00:00')))->modify('-23 hours');
        elseif ( $toDate == $aWeekAgo ) $toDate = (new \DateTime($NOW->format('Y-m-d 00:00:00')))->modify('-6 days');
        elseif ( $toDate == $aMonthAgo ) $toDate = (new \DateTime($NOW->format('Y-m-d 00:00:00')))->modify('-29 days');
        elseif ( $toDate == $threeMonthAgo ) $toDate = (new \DateTime($NOW->format('Y-m-d 00:00:00')))->modify('-89 days');
        elseif ( $toDate == $oneYearAgo ) $toDate = (new \DateTime($NOW->format('Y-m-d 00:00:00')))->modify('-364 days');
        //elseif( $toDate == null ) $toDate = new \DateTime('now');

        return ['from' => $fromDate, 'to' => $toDate];
    }

    /**
     * @param Presentation $presentation
     * @param $combinations
     * @param $filters Filters
     * @return mixed
     */
    abstract protected function feedData($presentation, $combinations, $filters);
}