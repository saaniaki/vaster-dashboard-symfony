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
    public function __construct(Module $module, ManagerRegistry $managerRegistry)
    {
        parent::__construct($module, $managerRegistry);

        //General Properties
        $this->type = 'pie';
        $this->size = 200;
        $this->tooltip_shared = 0; // false but false is null so 0
        $this->all_data[] = [
            'name' => $this->subModule->getUnit(),
            'data' => null,
            'type' => 'pie'
        ];
    }

    protected function feedData($presentation, $combinations, $filters, $removeZeros){
        /** @var Combination $combo */
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
    }
}