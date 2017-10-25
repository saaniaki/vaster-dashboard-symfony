<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 28/07/17
 * Time: 5:49 PM
 */

namespace AppBundle\Module;


use AppBundle\Entity\Module;
use AppBundle\Entity\ModuleInfo;
use AppBundle\Library\ConditionTree;
use AppBundle\Library\Node;
use AppBundle\Library\SearchBinaryTree;
use AppBundle\Library\Stack;
use AppBundle\Module\Configuration\Configuration;
use AppBundle\Module\Configuration\Filters;
use AppBundle\Module\Configuration\HighChartResult;
use AppBundle\Module\Configuration\Presentation;
use AppBundle\Module\Configuration\Settings;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ManagerRegistry;

abstract class AbstractModule implements ModuleInterface
{
    /** @var Settings */
    protected $configuration; //needs getter and setter
    /** @var ModuleInfo */
    protected $moduleInfo; //needs getter and setter
    /** @var ArrayCollection */
    protected $dataSets;
    /** @var HighChartResult */
    protected $result;

    /**
     * Parsing the database data to a VDP-Module Object.
     * Upon the creation of any module, the needed data
     * is being grabbed using the configuration of it
     * To avoid duplication adn ease of use, Module Entity
     * is not being stored.
     * Instead, two useful objects, Configuration and
     * ModuleInfo are stored inside this object.
     * @param Module $module
     * @param ManagerRegistry $managerRegistry
     */
    public function __construct(Module $module, ManagerRegistry $managerRegistry)
    {
        //Setting up the main properties of a module
        //Grabbing the configuration JSON from the database, validate it, parse it, and storing the Configuration Object
        $this->setConfiguration($module->getConfiguration());
        //Grabbing the ModuleInfo data from the database and parse it to a ModuleInfo Object
        $this->setModuleInfo($module->getModuleInfo());

        //This is the data source object which would be used to grab the data from the database
        $data_source = $this->getConfiguration()->getDataSource();
        $query = $data_source->initQuery();                                                     // Creating the base query
        $query = $data_source->applyCondition($query, $this->getConfiguration()->getFilters());      // Applying the filters to the base query

        //Applying each category condition and inserting the result into an array collection
        $this->setDataSets(new ArrayCollection());
        $categories = $this->getConfiguration()->getCategories();
        if(!$categories->isEmpty()){
            foreach ($categories as $category){
                $catQuery = $data_source->applyCondition(clone $query, $category);                        // Applying the categories to the base query
                $this->getDataSets()->set($category->getTitle(), $catQuery->getQuery()->getArrayResult());
            }
        }else $this->getDataSets()->set('All', $query->getQuery()->getArrayResult());

        $this->setResult(new HighChartResult($module->getId(),'Get title from presentation'));
    }

    /**
     * @return array
     */
    public function render()
    {
        return $this->processData()->extract();
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
     * @return HighChartResult
     */
    abstract protected function processData();

    /**
     * @return Settings
     */
    public function getConfiguration(): Settings
    {
        return $this->configuration;
    }

    /**
     * @param Settings $configuration
     */
    public function setConfiguration(Settings $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @return ModuleInfo
     */
    public function getModuleInfo(): ModuleInfo
    {
        return $this->moduleInfo;
    }

    /**
     * @param ModuleInfo $moduleInfo
     */
    public function setModuleInfo(ModuleInfo $moduleInfo)
    {
        $this->moduleInfo = $moduleInfo;
    }

    /**
     * @return ArrayCollection
     */
    public function getDataSets(): ArrayCollection
    {
        return $this->dataSets;
    }

    /**
     * @param ArrayCollection $dataSets
     */
    public function setDataSets(ArrayCollection $dataSets)
    {
        $this->dataSets = $dataSets;
    }

    /**
     * @return HighChartResult
     */
    public function getResult(): HighChartResult
    {
        return $this->result;
    }

    /**
     * @param HighChartResult $result
     */
    public function setResult(HighChartResult $result)
    {
        $this->result = $result;
    }
}