<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 30/08/17
 * Time: 12:01 PM
 */

namespace AppBundle\Module\Graph;

use AppBundle\Entity\Module;
use AppBundle\Module\AbstractModule;
use AppBundle\Module\Combination;
use Doctrine\Common\Persistence\ManagerRegistry;

class Pie extends AbstractModule
{
    private $processedData;

    public function __construct(Module $module, ManagerRegistry $managerRegistry)
    {
        parent::__construct($module, $managerRegistry);
        //General Properties
        $this->processedData[] = [
            'name' => 'name',
            'data' => null,
            'type' => 'pie'
        ];
    }

    protected function processData()
    {
        foreach ($this->getDataSets() as $name => $set)
            $this->addData([
                'y' => count($set),
                'name' => $name
            ]);

        $this->getResult()->setAllData($this->getProcessedData());

        //dump($this->getDataSets());die();

        return $this->getResult();
    }

    private function addData(array $array){
        $this->processedData[0]['data'][] = $array;
    }

    private function getProcessedData(){
        return $this->processedData;
    }

/*
        $this->all_data[] = [
            'name' => $this->subModule->getUnit(),
            'data' => null,
            'type' => 'pie'
        ];

        $this->all_data[] = [
                'name' => $name,
                'data' => $data,
                'type' => 'column',
                'yAxis' => 0,
                'color' => $this->color->current()
            ];

        $removeZeros = !$presentation->isZero();

        /** @var Combination $combo *
        foreach( $combinations as $combo){
            $name = $combo->getName();
            if( $name == null) $name .= $this->subModule->getGeneralName();
            $number = $this->subModule->count($combo, $filters);
            $this->process($number, $name, $removeZeros);
        }
        $this->footer = $this->subModule->getFooter($this->currentlyShowing);
        return $combinations;
    }

    private function process($number, $name, $removeZeros){
        if( !$removeZeros || $number != 0 ){
            $this->all_data[0]['data'][] = [
                'y' => $number,
                'name' => $name
            ];
            $this->currentlyShowing += $number;
        }

*/

}