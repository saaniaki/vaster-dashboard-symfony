<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 20/10/17
 * Time: 4:51 PM
 */

namespace AppBundle\Module\Configuration;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class HighCartResult
 * Instances of this class store the data needed
 * to be passed to High Charts API.
 * NOTE: No data processing is being done here
 * @package AppBundle\Module\Configuration
 */
class HighChartResult
{
    private $id;

    private $title;
    private $footer;
    /** @var ArrayCollection */
    private $color;
    private $type;
    private $size;

    private $tooltip_shared;
    private $currentlyShowing = 0;
    private $allData;

    private $xTitle;
    private $xAxisType; //linear, logarithmic, datetime or category
    private $start = [
        'year' => 2016,
        'month' => 11,
        'day' => 9,
    ];


    private $yTitle;
    private $yAllowDecimals;


    private $y1Title;
    private $y1Format;
    private $y1AllowDecimals;
    /** @var  $xInterval integer */
    private $xInterval;

    //Setting up the default values which are the same fore all modules using HighCharts
    function __construct(int $id, string $title = null)
    {
        $this->setId($id);

        if($title == null) $this->setTitle("New Graph");
        else $this->setTitle($title);

        $this->setColor(new ArrayCollection(['#2f7ed8', '#0d233a', '#8bbc21', '#910000', '#1aadce', '#492970', '#f28f43', '#77a1e5', '#c42525', '#a6c96a']));
        $this->setSize(200);
        $this->setTooltipShared(0);             // false but false is null so 0
        $this->setCurrentlyShowing(0);
        $this->setFooter("No Description");
    }

    public function extract(){
        return get_object_vars($this);
    }






    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getFooter()
    {
        return $this->footer;
    }

    /**
     * @param mixed $footer
     */
    public function setFooter($footer)
    {
        $this->footer = $footer;
    }

    /**
     * @return ArrayCollection
     */
    public function getColor(): ArrayCollection
    {
        return $this->color;
    }

    /**
     * @param ArrayCollection $color
     */
    public function setColor(ArrayCollection $color)
    {
        $this->color = $color;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param mixed $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * @return int
     */
    public function getTooltipShared(): int
    {
        return $this->tooltip_shared;
    }

    /**
     * @param int $tooltip_shared
     */
    public function setTooltipShared(int $tooltip_shared)
    {
        $this->tooltip_shared = $tooltip_shared;
    }

    /**
     * @return int
     */
    public function getCurrentlyShowing(): int
    {
        return $this->currentlyShowing;
    }

    /**
     * @param int $currentlyShowing
     */
    public function setCurrentlyShowing(int $currentlyShowing)
    {
        $this->currentlyShowing = $currentlyShowing;
    }

    /**
     * @return mixed
     */
    public function getAllData()
    {
        return $this->allData;
    }

    /**
     * @param mixed $allData
     */
    public function setAllData($allData)
    {
        $this->allData = $allData;
    }

}