<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 26/06/17
 * Time: 4:58 PM
 */

namespace AppBundle\Module\Graph;

use AppBundle\Entity\Module;
use AppBundle\Module\Configuration\Configuration;
use AppBundle\Module\ModuleInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\QueryBuilder;


class Registration implements ModuleInterface
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

    private $currentlyShowing = 0;

    private $footer;


    private $tooltip_shared = 1;
    private $all_data = [];

    public function __construct(Module $module, ManagerRegistry $managerRegistry)
    {
        $this->module = $module;
        $em = $managerRegistry->getManager('vaster');
        $this->userRep = $em->getRepository("VasterBundle:User");

        $this->size = 300; //useless

        $this->xAxisType = 'datetime';
        $this->xTitle = 'Time';
        $this->yTitle = 'Registration';
        $this->y1Title = 'Registration Cumulative';
        //$this->data1_yAxis = 1;
        $this->y1Format = '{value}'; //'{value}%'
        //$this->yMax = 10;
        //$this->y1Max = 100;

        //$this->data1_tooltip = 'percentage';

        $this->yAllowDecimals = false;
        $this->y1AllowDecimals = false;

        $this->color = new ArrayCollection(['#2f7ed8', '#0d233a', '#8bbc21', '#910000', '#1aadce', '#492970', '#f28f43', '#77a1e5', '#c42525', '#a6c96a']);
    }

    /**
     * @param Configuration|ArrayCollection $configuration
     * @return array
     */
    public function render(Configuration $configuration)
    {
        $presentation = $configuration->getPresentation(); //this value should be valued!!

        $filters = (array) $configuration->getFilters();
        $removeZeros = $configuration->isRemoveZeros();

        /*
         * getting all the possible categories
         */
        $categories = [];
        $singleCategories = $configuration->getCategories()->getSingle();
        $multiCategories = new ArrayCollection((array) $configuration->getCategories()->getMulti());

        foreach ($singleCategories as $cat){
            $categories[strtolower($cat)] = $this->module->getModuleInfo()->getAvailableConfiguration()['filters'][strtolower($cat)];//get actual values from module info
        }


        foreach ($multiCategories as $type => $cat){
            foreach ($cat as $catName => $value) {
                $categories[$catName] = [
                    '0' => ['match' => false, 'value' => $value, 'type' => $type],
                    '1' => ['match' => true, 'value' => $value, 'type' => $type]
                ];
            }
        }





        $this->makeNames($presentation, $this->combinations($categories), $filters, $removeZeros);
        $this->title = 'Registration Over Time';
        $totalUsers = $this->userRep->createQueryBuilder('user')->select('COUNT(user)')->getQuery()->getSingleScalarResult();
        $this->footer = "Total Users: " . $totalUsers . " / Currently showing: " . $this->currentlyShowing;

        return get_object_vars($this);
    }


    private function process($rawData, $startNumber, $name, $removeZeros)
    {
        $data = [];
        $data_cumulative = [];


        $max = 0;
        $filteredUsers = 0;
        $sum = $startNumber;
        foreach ($rawData as $dot) {
            $temp = [
                'y' => $dot['number'],
                'name' => "from " . $dot['from']->format('Y-m-d H:i') . " to " . $dot['to']->format('Y-m-d H:i')
            ];
            //array_push($this->data, $temp);
            array_push($data, $temp);

            if ($max < $dot['number'])
                $max = $dot['number'];

            $filteredUsers += $dot['number'];
            $sum += $dot['number'];
            $temp['y'] = $sum;
            //array_push($this->data1, $temp);
            array_push($data_cumulative, $temp);
        }

        if (!$removeZeros || $sum != 0){

            $this->currentlyShowing += $filteredUsers;
/*
            if( count($this->all_data) == 0 )
                $this->footer .= " $name: " . $sum;
            else
                $this->footer .= ", $name: " . $sum;
*/
            $this->all_data[] = [
                'name' => $name,
                'data' => $data,
                'type' => 'column',
                'yAxis' => 0,
                'color' => $this->color->current()
            ];

            $this->all_data[] = [
                'name' => $name . ' Cumulative',
                'data' => $data_cumulative,
                'type' => 'line',
                'yAxis' => 1,
                'color' => $this->color->current()
            ];
        }
        $this->color->next();
        //$this->yMax = $max;

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
            //dump($query->getQuery());die();
            $column = $query->getQuery()->getArrayResult();




            $from = ($filters['date']['period'])->getFrom();
            $to = ($filters['date']['period'])->getTo();
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
            $temp = clone $newFilters['date']['period']; //error id 'period is not there
            $temp->setFrom(null);
            $temp->setTo($from->format('Y-m-d'));
            $newFilters['date']['period'] = $temp;

            $query = $this->userRep->createQueryBuilder('user')->select('COUNT(user)')
                ->orderBy('user.createdtime', 'DESC');
            $query->leftJoin('user.account', 'account');        //should join dynamically (NOT USEFUL FOR ALL QUERIES)
            $query->leftJoin('user.profession', 'profession');  //should join dynamically (NOT USEFUL FOR ALL QUERIES)
            $query = $this->userRep->applyFilters($newFilters, $query);
            $catArray = $this->userRep->applyCategories($combo, $query);


            $name = $catArray['name'];
            if( $name == null ) $name .= "All Users";
            /** @var $query QueryBuilder */
            $query = $catArray['query'];

            $startNumber = $query->getQuery()->getSingleScalarResult();




            if( $presentation == 'Hourly' ){
                $interval = new \DateInterval('PT1H');
                $this->xInterval = 3600 * 1000;
            } elseif( $presentation == 'Daily' ){
                $interval = new \DateInterval('P1D');
                $this->xInterval = 24 * 3600 * 1000;
            } elseif( $presentation == 'Weekly' ){
                $interval = new \DateInterval('P7D');
                $this->xInterval = 7 * 24 * 3600 * 1000;
            } else { // default is daily
                $interval = new \DateInterval('P7D');
                $this->xInterval = 7 * 24 * 3600 * 1000;
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