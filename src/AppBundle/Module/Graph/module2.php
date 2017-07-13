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
use Doctrine\ORM\QueryBuilder;


class module2 implements ModuleInterface
{
    /**
     * @var Module
     */
    private $module;
    private $userRep;

    private $title;
    private $size;
    private $color;


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
    private $yAllowDecimals;


    private $y1Title;
    private $y1Format;
    /** @var  $y1Max integer */
    private $y1Max;
    private $y1AllowDecimals;


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



    private $all_data = [];

    public function __construct(Module $module, ManagerRegistry $managerRegistry)
    {
        $this->module = $module;
        $em = $managerRegistry->getManager('vaster');
        $this->userRep = $em->getRepository("VasterBundle:User");

        //$this->type = 'column';
        //$this->type1 = 'line';
        $this->size = 300; //useless

        $this->xAxisType = 'datetime';
        $this->xTitle = 'Time';
        $this->yTitle = 'Registration';
        $this->y1Title = 'Percentage';
        $this->data1_yAxis = 1;
        $this->y1Format = '{value}'; //'{value}%'
        //$this->yMax = 10;
        //$this->y1Max = 100;

        //$this->data1_tooltip = 'percentage';

        $this->yAllowDecimals = false;
        $this->y1AllowDecimals = false;

        $this->color = new ArrayCollection(['#2f7ed8', '#0d233a', '#8bbc21', '#910000', '#1aadce', '#492970', '#f28f43', '#77a1e5', '#c42525', '#a6c96a']);
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
        $presentation = $configuration['presentation']; //this value should be parsed!! and checked

        $filters = $configuration['filters'];
        $removeZeros = $configuration['remove_zeros'];

        /*
         * getting all the possible categories
         */
        $categories = [];
        $singleCategories = $configuration['categories']['single'];
        $multiCategories = new ArrayCollection($configuration['categories']['multi']);

        foreach ($singleCategories as $cat){
            $categories[$cat] = $this->module->getModuleInfo()->getAvailableConfiguration()['filters'][$cat];//get actual values from module info
        }

        foreach ($multiCategories as $type => $cat){
            foreach ($cat as $catName => $value) {
                $categories[$catName] = [
                    '0' => ['match' => false, 'value' => $value, 'type' => $type],
                    '1' => ['match' => true, 'value' => $value, 'type' => $type]
                ];
            }
        }




        $totalUsers = $this->userRep->createQueryBuilder('user')->select('COUNT(user)')->getQuery()->getSingleScalarResult();

        $this->title = 'Registration Over Time';
        $this->footer = "Total Users: " . $totalUsers . " / Currently showing: (";

        $this->makeNames($presentation, $this->combinations($categories), $filters, $removeZeros);

        $this->footer .= ")";
        return get_object_vars($this);
    }


    private function process($rawData, $startNumber, $name, $removeZeros)
    {
        $data_newVersion = [];
        $data_cumulative_newVersion = [];


        $max = 0;
        $filteredUsers = 0;
        $sum = $startNumber;
        foreach ($rawData as $dot) {
            $temp = [
                'y' => $dot['number'],
                'name' => "from " . $dot['from']->format('Y-m-d H:i') . " to " . $dot['to']->format('Y-m-d H:i')
            ];
            //array_push($this->data, $temp);
            array_push($data_newVersion, $temp);

            if ($max < $dot['number'])
                $max = $dot['number'];

            $filteredUsers += $dot['number'];
            $sum += $dot['number'];
            $temp['y'] = $sum;
            //array_push($this->data1, $temp);
            array_push($data_cumulative_newVersion, $temp);
        }

        if (!$removeZeros || $sum != 0){

            if( count($this->all_data) == 0 )
                $this->footer .= " $name: " . $filteredUsers;
            else
                $this->footer .= ", $name: " . $filteredUsers;

            $this->all_data[] = [
                'name' => $name,
                'data' => $data_newVersion,
                'type' => 'column',
                'yAxis' => 0,
                'color' => $this->color->current()
            ];

            $this->all_data[] = [
                'name' => $name . ' Cumulative',
                'data' => $data_cumulative_newVersion,
                'type' => 'line',
                'yAxis' => 1,
                'color' => $this->color->current()
            ];
        }
        $this->color->next();
        $this->yMax = $max;

    }

    private function combinations($data){ //should gp to abstract class
        $combinations = [[]];
        $comKeys = array_keys($data);

        for ($count = 0; $count < count($comKeys); $count++) {
            $tmp = [];
            foreach ($combinations as $v1) {
                foreach ($data[$comKeys[$count]] as $v2)
                    $tmp[] = $v1 + [$comKeys[$count] => $v2];

            }
            $combinations = $tmp;
        }

        return $combinations;
    }

    private function makeNames($presentation, $combinations, $filters, $removeZeros){

        foreach( $combinations as $combo){

            $query = $this->userRep->createQueryBuilder('user')->select('user.createdtime, user.accounttype')
                ->orderBy('user.createdtime', 'DESC');
            $query->leftJoin('user.account', 'account');        //should join dynamically (NOT USEFUL FOR ALL QUERIES)
            $query->leftJoin('user.profession', 'profession');  //should join dynamically (NOT USEFUL FOR ALL QUERIES)
            $query = $this->userRep->applyFilters($filters, $query);
            $catArray = $this->userRep->applyCategories($combo, $query);
            $name = $catArray['name'];
            /** @var $query QueryBuilder */
            $query = $catArray['query'];
            $column = $query->getQuery()->getArrayResult();


            $from = current(current($filters['date']))['from'];
            $to = current(current($filters['date']))['to'];
            $adjustedDates = $this->userRep->adjustDate($from, $to);
            /** @var \DateTime $from $to */
            $from = $adjustedDates['from'];
            $to = $adjustedDates['to'];

            $this->start = [
                'year' => $from->format('Y'),
                'month' => $from->format('m') - 1,
                'day' => $from->format('d'),
            ];


            //Calculating the starting number
            $newFilters = $filters;
            $temp = &$newFilters['date']['period'][0]; //error id 'period is not there
            $temp['from'] = null;
            $temp['to'] = $from->format('Y-m-d');

            $query = $this->userRep->createQueryBuilder('user')->select('COUNT(user)')
                ->orderBy('user.createdtime', 'DESC');
            $query->leftJoin('user.account', 'account');        //should join dynamically (NOT USEFUL FOR ALL QUERIES)
            $query->leftJoin('user.profession', 'profession');  //should join dynamically (NOT USEFUL FOR ALL QUERIES)
            $query = $this->userRep->applyFilters($newFilters, $query);
            $catArray = $this->userRep->applyCategories($combo, $query);
            $name = $catArray['name'];
            /** @var $query QueryBuilder */
            $query = $catArray['query'];
            $startNumber = $query->getQuery()->getSingleScalarResult();





            if( $presentation == 'hourly' ){
                $interval = new \DateInterval('PT1H');
                $this->xInterval = 3600 * 1000;
            } elseif( $presentation == 'daily' ){
                $interval = new \DateInterval('P1D');
                $this->xInterval = 24 * 3600 * 1000;
            } elseif( $presentation == 'weekly' ){
                $interval = new \DateInterval('P7D');
                $this->xInterval = 7 * 24 * 3600 * 1000;
            } else { // default is daily
                $interval = new \DateInterval('P1D');
                $this->xInterval = 24 * 3600 * 1000;
            }


            $count = 0;
            $result = [];
            while( !($from > $to) ){
                $intervalEnd = clone $from;
                $intervalEnd->add($interval);

                $number = 0;
                /** @var $item \DateTime */
                foreach ( $column as $item ){
                    if( $from < $item['createdtime'] && $item['createdtime'] < $intervalEnd ){
                        array_pop($column);
                        $number++;
                    }
                }

                $result[$count] = [
                    'from' => clone $from,
                    'to' => $intervalEnd,
                    'number' => $number
                ];
                $count++;
                $from->add($interval);
            }


            $this->process($result, $startNumber, $name, $removeZeros);

        }

        return $combinations;
    }

}