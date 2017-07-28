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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ManagerRegistry;

abstract class AbstractModule implements ModuleInterface
{
    /**
     * @var Module
     */
    protected $module;
    protected $userRep;


    protected $title;
    protected $footer;
    protected $color;
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


    protected $yTitle;
    protected $yAllowDecimals;


    protected $y1Title;
    protected $y1Format;
    protected $y1AllowDecimals;
    /** @var  $xInterval integer */
    protected $xInterval;

    public function __construct(Module $module, ManagerRegistry $managerRegistry)
    {
        $this->module = $module;
        $em = $managerRegistry->getManager('vaster');
        $this->userRep = $em->getRepository("VasterBundle:User");
        $this->color = new ArrayCollection(['#2f7ed8', '#0d233a', '#8bbc21', '#910000', '#1aadce', '#492970', '#f28f43', '#77a1e5', '#c42525', '#a6c96a']);
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

        foreach ($singleCategories as $cat){
            $categories[strtolower($cat)] = $this->module->getModuleInfo()->getAvailableConfiguration()['filters'][strtolower($cat)];//get actual values from module info
        }

        foreach ($searchCategories as $catName => $value){
            $categories[$catName][] = $value;
            $value = clone $value;
            $value->setNegate(!$value->isNegate());
            $categories[$catName][] = $value;
        }

        foreach ($dateCategories as $catName => $value){
            $categories[$catName][] = $value;
            $value = clone $value;
            $value->setNegate(!$value->isNegate());
            $categories[$catName][] = $value;
        }


        $this->feedData($presentation, $this->combinations($categories), $filters, $removeZeros);
        $totalUsers = $this->userRep->createQueryBuilder('user')->select('COUNT(user)')->getQuery()->getSingleScalarResult();
        $this->footer = "Total Users: " . $totalUsers . " / Currently showing: " . $this->currentlyShowing;

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

    protected function setTitle($default){
        $title = $this->module->getConfiguration()->getLayout()->getTitle();
        if($title == null) $this->title = $default;
        else $this->title = $title;
    }

    /**
     * @param $presentation
     * @param $combinations
     * @param $filters Filters
     * @param $removeZeros
     * @return mixed
     */
    abstract protected function feedData($presentation, $combinations, $filters, $removeZeros);
}