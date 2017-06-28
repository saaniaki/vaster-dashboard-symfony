<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 26/06/17
 * Time: 4:58 PM
 */

namespace AppBundle\Module\Graph;

use AppBundle\Entity\Module;
use AppBundle\Module\ModuleInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;


class module2 implements ModuleInterface
{
    /**
     * @var Module
     */
    private $module;
    private $userRep;

    private $title;
    private $size;
    private $color = ['#2f7ed8', '#0d233a', '#8bbc21', '#910000', '#1aadce', '#492970', '#f28f43', '#77a1e5', '#c42525', '#a6c96a'];


    private $xTitle;
    private $xAxisType; //linear, logarithmic, datetime or category
    private $start = [
        'year' => 2016,
        'month' => 11,
        'day' => 9,
    ];


    private $yTitle;
    private $yFormat;
    /** @var  $yMax integer */
    private $yMax;

    private $y1Title;
    private $y1Format;
    /** @var  $y1Max integer */
    private $y1Max;


    private $xValues;
    private $yValues;

    /** @var  $xInterval integer */
    private $xInterval;


    private $type;
    private $data = [];
    private $data_name;
    private $data_tooltip; //percentage
    /** @var  $data_yAxis integer */
    private $data_yAxis;

    private $type1;
    private $data1 = [];
    private $data1_name;
    private $data1_tooltip; //percentage
    /** @var  $data1_yAxis integer */
    private $data1_yAxis;

    private $footer;


    //for this module only
    private $rawData;

    public function __construct(Module $module, ManagerRegistry $managerRegistry)
    {
        date_default_timezone_set('UTC'); //remove when server is on UTC

        $this->module = $module;
        $em = $managerRegistry->getManager('vaster');
        $this->userRep = $em->getRepository("VasterBundle:User");

        $this->type = 'column';
        $this->type1 = 'line';
        $this->size = 300;

        $this->xAxisType = 'datetime';
        $this->xTitle = 'Time';
        $this->yTitle = 'Registration';
        $this->y1Title = 'Percentage';
        $this->data1_yAxis = 1;
        $this->y1Format = '{value}%';
        $this->y1Max = 100;

        $this->data1_tooltip = 'percentage';
    }

    /**
     * 'userType' => 'all', 'standard', 'internal'
     * 'keyword' => null, $keyword
     * 'analytics' => 'device-type', 'user-type', 'availability', 'device-type/user-type', 'availability/user-type', 'availability/device-type', 'mix'
     *
     * EX:
     * ['userType' => 'all', 'keyword' => null, 'analytics' => 'availability/device-type']
     *
     *
     * @param ArrayCollection $configuration
     * @return array
     */
    public function render(ArrayCollection $configuration)
    {
        $analytics = $configuration['analytics'];
        $type = $configuration['userType'];
        $keyword = $configuration['keyword'];

        if(  in_array($analytics, $this->module->getModuleInfo()->getAvailableAnalytics()) )
            call_user_func_array(array($this, $analytics), array($type, $keyword));
        else die('bad configuration');



        $this->title = 'Registration Over Time';
        $this->data_name = 'Users';
        $this->data1_name = 'Cumulative';
        $totalUsers = $this->userRep->count('all');
        $this->footer = "Total Users: " . $totalUsers;


        $this->proccess($this->rawData, $totalUsers);


        return get_object_vars($this);
    }

    public function default($type, $keyword){
        return $this->daily($type, $keyword);
    }

    public function hourly($type, $keyword){
        $now = new \DateTime('now');
        $this->rawData = $this->userRep->registrationNumber($type, $keyword, new \DateTime('2016-12-09'), $now, new \DateInterval('PT1H'));
        $this->xInterval = 3600 * 1000;
    }

    public function daily($type, $keyword){
        $now = new \DateTime('now');
        $this->rawData = $this->userRep->registrationNumber($type, $keyword, new \DateTime('2016-12-09'), $now, new \DateInterval('P1D'));
        $this->xInterval = 24 * 3600 * 1000;
    }

    public function weekly($type, $keyword){
        $now = new \DateTime('now');
        $this->rawData = $this->userRep->registrationNumber($type, $keyword, new \DateTime('2016-12-09'), $now, new \DateInterval('P7D'));
        $this->xInterval = 7 * 24 * 3600 * 1000;
    }

    private function proccess($rawData, $totalUsers){
        $sum = 0;
        foreach ( $rawData as $dot ){
            $temp = [
                'y' => $dot['number'],
                'name' => "from " . $dot['from']->format('Y-m-d H:i') . " to " . $dot['to']->format('Y-m-d H:i')
            ];
            array_push($this->data, $temp);
            $sum += $dot['number'];
            $temp = [
                'y' => ($sum/$totalUsers)*100,
                'name' => "from " . $dot['from']->format('Y-m-d H:i') . " to " . $dot['to']->format('Y-m-d H:i')
            ];
            array_push($this->data1, $temp);
        }
    }
}