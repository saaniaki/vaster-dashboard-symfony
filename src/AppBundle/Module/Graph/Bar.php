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

use Doctrine\Common\Persistence\ManagerRegistry;


class Bar extends AbstractModule
{
    private $processedData;

    public function __construct(Module $module, ManagerRegistry $managerRegistry)
    {
        parent::__construct($module, $managerRegistry);
        //General Properties
        $this->processedData = [];

        /*
        $this->xAxisType = 'datetime';
        $this->xTitle = 'Time';
        $this->y1Format = '{value}'; //'{value}%'
        //$this->yMax = 10;
        //$this->y1Max = 100;
        //$this->data1_tooltip = 'percentage';
        $this->yAllowDecimals = false;
        $this->y1AllowDecimals = false;
        */
    }


    protected function processData(){
        $data_source_field = $this->getConfiguration()->getDataSource()->getField();

        $xAxisCategories = [];

        if( $data_source_field->isSnapshot() && !$data_source_field->isDateType() ){

            $fullGrouped = [];
            foreach ($this->getDataSets() as $name => $set){

                $dateGroups = [];
                foreach ( $set as $item ){

                    $dateGroup = $item['dateGroup']->format('Y-m-d');

                    if(!array_key_exists($dateGroup, $dateGroups)) $dateGroups[$dateGroup] = [$item];
                    else $dateGroups[$dateGroup][] = $item;

                    //creating the xAxis categories
                    if(!in_array($dateGroup, $xAxisCategories)) $xAxisCategories[] = $dateGroup;

                }
                $fullGrouped[$name] = $dateGroups;
            }

            $this->getResult()->setXAxisCategories($xAxisCategories);

            $categoriesCount = [];
            foreach ($fullGrouped as $name => $group){

                $counts = [];
                foreach ($group as $dateGroup => $set){


                    $counts[] = count($set);

                }
                $categoriesCount[$name] = $counts;
            }


            foreach ($this->getDataSets() as $name => $set)
                $this->addData([
                    'name' => $name,
                    'data' => $categoriesCount[$name],
                    'type' => 'column',
                ]);


            $this->getResult()->setAllData($this->getProcessedData());
            return $this->getResult();
        } else if( !$data_source_field->isSnapshot() && $data_source_field->isDateType() ){

            $this->getResult()->setXTitle('Time');
            $this->getResult()->setXAxisType('datetime');




            //----------------------------------------------------
            $interval = new \DateInterval('P1D');
            $this->getResult()->setXInterval( 24 * 3600 * 1000);

            // setting up the $from and $to if it's not defined
            $from = new \DateTime();
            $to = new \DateTime();
            foreach ($this->getDataSets() as $name => $set){
                /** @var \DateTime $temp */
                $temp = current($set)[$data_source_field->getMachineTitle()];
                if($from > $temp) $from = $temp;
            }
            $from = new \DateTime($from->format('Y-m-d 00:00:00'));
            $to = new \DateTime($to->format('Y-m-d 00:00:00'));

            $this->getResult()->setStart([
                'year' => $from->format('Y'),
                'month' => $from->format('m') - 1,
                'day' => $from->format('d'),
                'hour' => $from->format('H'),
                'minute' => $from->format('i')
            ]);


            //counting the matched data for each interval

            foreach ($this->getDataSets() as $name => $set){
                $result = [];
                $flexFrom = clone $from;

                while( !($flexFrom > $to) ){
                    $intervalEnd = (clone $flexFrom)->add($interval);

                    $number = 0;
                    foreach ( $set as $item ){
                        if( $flexFrom < $item[$data_source_field->getMachineTitle()] && $item[$data_source_field->getMachineTitle()] < $intervalEnd ){
                            unset($item);
                            $number++;
                        }
                    }

                    $result[] = [
                        'name' => 'From ' . (clone $flexFrom)->format('Y-m-d H:i') . " to " . $intervalEnd->format('Y-m-d H:i'),
                        'y' => $number
                    ];
                    $flexFrom->add($interval);

                }


                $this->addData([
                    'name' => $name,
                    'data' => $result,
                    'type' => 'column',
                ]);
            }





            $this->getResult()->setAllData($this->getProcessedData());
            return $this->getResult();

        } else {
            foreach ($this->getDataSets() as $name => $set)
                $this->addData([
                    'name' => $name,
                    'data' => [count($set)],
                    'type' => 'column',
                ]);

            $this->getResult()->setAllData($this->getProcessedData());
            return $this->getResult();
        }

    }

    private function addData(array $array){
        $this->processedData[] = $array;
    }

    private function getProcessedData(){
        return $this->processedData;
    }

}