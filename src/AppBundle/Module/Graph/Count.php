<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 12/06/17
 * Time: 3:19 PM
 */

namespace AppBundle\Module\Graph;


use AppBundle\Entity\Module;
use AppBundle\Module\AbstractModule;
use AppBundle\Module\Combination;
use AppBundle\Module\Configuration\Configuration;
use AppBundle\Module\ModuleInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\QueryBuilder;

class Count extends AbstractModule
{
    public function __construct(Module $module, ManagerRegistry $managerRegistry)
    {
        parent::__construct($module, $managerRegistry);

        $this->setTitle('User Count');
        $this->type = 'pie';
        $this->size = 200;
        $this->tooltip_shared = 0; // false but false is null so 0
    }

    protected function feedData($presentation, $combinations, $filters, $removeZeros){
        $data = [];
        /** @var Combination $combo */
        foreach( $combinations as $combo){

            //dump($combo);

            $query = null;
            if( $presentation == 'User Count' ){
                $query = $this->userRep->createQueryBuilder('user')->select('COUNT(user)')
                    ->orderBy('user.createdtime', 'DESC');
            } else {
                $query = $this->userRep->createQueryBuilder('user')->select('COUNT(user)')
                    ->orderBy('user.createdtime', 'DESC');
            }


            $query->leftJoin('user.account', 'account');        //should join dynamically (NOT USEFUL FOR ALL QUERIES)
            $query->leftJoin('user.profession', 'profession');  //should join dynamically (NOT USEFUL FOR ALL QUERIES)
            $query = $this->userRep->applyFilters($filters, $query);
            $query = $this->userRep->applyCategories($combo, $query);

            $name = $combo->getName();
            if( $name == null ) $name .= "All Users";

            $number = $query->getQuery()->getSingleScalarResult();
            //process
            if( !$removeZeros || $number != 0 ){
                //$this->data_name = 'Users';
                $data[] = [
                    'y' => $number,
                    'name' => $name
                ];
                $this->currentlyShowing += $number;
            }

        }
        //process
        $this->all_data[] = [
            'name' => 'Users',
            'data' => $data,
            'type' => 'pie'
        ];

        $totalUsers = $this->userRep->createQueryBuilder('user')->select('COUNT(user)')->getQuery()->getSingleScalarResult();
        $this->footer = "Total Users: " . $totalUsers . " / Currently showing: " . $this->currentlyShowing;

        return $combinations;
    }


}