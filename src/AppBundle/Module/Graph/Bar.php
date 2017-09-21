<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 2017-08-20
 * Time: 9:38 AM
 */

namespace AppBundle\Module\Graph;

use AppBundle\Entity\Module;
use AppBundle\Module\AbstractModule;
use AppBundle\Module\Combination;
use Doctrine\Common\Persistence\ManagerRegistry;

class Bar extends AbstractModule
{
    private $yFormat;
    /** @var  $yMax integer */
    private $yMax;
    /** @var  $y1Max integer */
    private $y1Max;

    public function __construct(Module $module, ManagerRegistry $managerRegistry)
    {
        parent::__construct($module, $managerRegistry);

        //General Properties
        $this->size = 200; //useless but no validation
        $this->xAxisType = 'datetime';
        $this->xTitle = 'Time';
        //$this->data1_yAxis = 1;
        $this->y1Format = '{value}'; //'{value}%'
        //$this->yMax = 10;
        //$this->y1Max = 100;
        //$this->data1_tooltip = 'percentage';

        $this->yAllowDecimals = false;
        $this->y1AllowDecimals = false;
    }


    protected function feedData($presentation, $combinations, $filters){
        $removeZeros = !$presentation->isZero();

        /** @var Combination $combo */
        foreach( $combinations as $combo){
            $name = $combo->getName();
            if( $name == null) $name .= $this->subModule->getGeneralName();


            $column = $this->subModule->getColumn($combo, $filters);

            $from = $filters->getDate()['period']->getFrom();
            $to = $filters->getDate()['period']->getTo();
            $adjustedDates = $this->adjustDate($from, $to);
            /** @var \DateTime $from $to */
            $from = $adjustedDates['from'];
            $to = $adjustedDates['to'];





            $startNumber = $this->subModule->getStartingNumber($from, $combo, $filters);

            $PR_interval = $presentation->getInterval();
            if( $PR_interval == 'Hourly' ){
                $interval = new \DateInterval('PT1H');
                $this->xInterval = 3600 * 1000;
            } elseif( $PR_interval == 'Daily' ){
                $interval = new \DateInterval('P1D');
                $this->xInterval = 24 * 3600 * 1000;
            } elseif( $PR_interval == 'Weekly' ){
                $interval = new \DateInterval('P7D');
                $this->xInterval = 7 * 24 * 3600 * 1000;
                //$from->modify('last week monday');
                $lastMonday = date('Y-m-d H:00', strtotime('previous monday', strtotime($from->format('Y-m-d H:00'))));
                $from = new \DateTime($lastMonday);
            } else {
                throw new \Exception("Bad module configuration: Interval is not defined properly.");
            }


            $this->start = [
                'year' => $from->format('Y'),
                'month' => $from->format('m') - 1,
                'day' => $from->format('d'),
                'hour' => $from->format('H'),
                'minute' => $from->format('i')
            ];


            $count = 0;
            $result = [];
            while( !($from > $to) ){
                $intervalEnd = clone $from;
                $intervalEnd->add($interval);

                $number = 0;
                foreach ( $column as $item ){
                    if( $from < $item['time'] && $item['time'] < $intervalEnd ){
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
        $this->footer = $this->subModule->getFooter($this->currentlyShowing);
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