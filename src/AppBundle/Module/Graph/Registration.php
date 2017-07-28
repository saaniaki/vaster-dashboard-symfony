<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 26/06/17
 * Time: 4:58 PM
 */

namespace AppBundle\Module\Graph;

use AppBundle\Entity\Module;
use AppBundle\Module\AbstractModule;
use AppBundle\Module\Combination;
use AppBundle\Module\Configuration\Configuration;
use AppBundle\Module\Configuration\Filters;
use AppBundle\Module\ModuleInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\QueryBuilder;


class Registration extends AbstractModule
{
    private $yFormat;
    /** @var  $yMax integer */
    private $yMax;
    /** @var  $y1Max integer */
    private $y1Max;

    public function __construct(Module $module, ManagerRegistry $managerRegistry)
    {
        parent::__construct($module, $managerRegistry);

        $this->setTitle('Registration Over Time');
        $this->size = 200; //useless but no validation
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
    }

    protected function feedData($presentation, $combinations, $filters, $removeZeros){
        /** @var Combination $combo */
        foreach( $combinations as $combo){

            $query = $this->userRep->createQueryBuilder('user')->select('user.createdtime, user.accounttype')
                ->orderBy('user.createdtime', 'DESC');
            $query->leftJoin('user.account', 'account');        //should join dynamically (NOT USEFUL FOR ALL QUERIES)
            $query->leftJoin('user.profession', 'profession');  //should join dynamically (NOT USEFUL FOR ALL QUERIES)
            $query = $this->userRep->applyFilters($filters, $query);
            $query = $this->userRep->applyCategories($combo, $query);
            $column = $query->getQuery()->getArrayResult();


            $from = $filters->getDate()['period']->getFrom();
            $to = $filters->getDate()['period']->getTo();
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
            $newFilters = clone $filters;
            $temp = clone $newFilters->getDate()['period']; //error key 'period' is not there
            $temp->setFrom(null);
            $temp->setTo($from->format('Y-m-d'));
            //$newFilters['date']['period'] = $temp;
            $newFilters->addDate('period', $temp);

            $query = $this->userRep->createQueryBuilder('user')->select('COUNT(user)')
                ->orderBy('user.createdtime', 'DESC');
            $query->leftJoin('user.account', 'account');        //should join dynamically (NOT USEFUL FOR ALL QUERIES)
            $query->leftJoin('user.profession', 'profession');  //should join dynamically (NOT USEFUL FOR ALL QUERIES)
            $query = $this->userRep->applyFilters($newFilters, $query);
            $query = $this->userRep->applyCategories($combo, $query);


            $name = $combo->getName();
            if( $name == null ) $name .= "All Users";

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

}