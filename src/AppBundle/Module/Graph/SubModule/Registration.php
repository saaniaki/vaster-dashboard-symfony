<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 2017-08-20
 * Time: 9:38 AM
 */

namespace AppBundle\Module\Graph\SubModule;

use AppBundle\Entity\Module;
use AppBundle\Module\AbstractModule;
use AppBundle\Module\Combination;
use AppBundle\Module\Configuration\Configuration;
use AppBundle\Module\Configuration\Filters;
use AppBundle\Module\Configuration\Presentation;
use AppBundle\Module\Graph\SubModule;
use AppBundle\Module\ModuleInterface;
use AppBundle\Module\SubModuleInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\QueryBuilder;


class Registration implements SubModuleInterface
{
    private $generalName = "All Users";
    private $unit = "Users";
    private $dbRepository;

    public function __construct(ModuleInterface $vdpModule, ObjectManager $em)
    {
        $this->dbRepository = $em->getRepository("VasterBundle:User");

        $vdpModule->setTitle('Registration Over Time');
        $vdpModule->yTitle = 'Registration';
        $vdpModule->y1Title = 'Registration Cumulative';
    }

    public function count(Combination $combo = null, Filters $filters = null){
        $query = $this->dbRepository->createQueryBuilder('user')->select('COUNT(user)')->orderBy('user.createdtime', 'DESC');
        $query->leftJoin('user.account', 'account');        //should join dynamically (NOT USEFUL FOR ALL QUERIES)
        $query->leftJoin('user.profession', 'profession');  //should join dynamically (NOT USEFUL FOR ALL QUERIES)
        $query = $this->dbRepository->applyFilters($filters, $query);
        $query = $this->dbRepository->applyCategories($combo, $query);
        return $query->getQuery()->getSingleScalarResult();
    }

    public function getColumn(Combination $combo = null, Filters $filters = null){
        $query = $this->dbRepository->createQueryBuilder('user')->select('user.createdtime as time, user.accounttype')
            ->orderBy('user.createdtime', 'DESC');
        $query->leftJoin('user.account', 'account');        //should join dynamically (NOT USEFUL FOR ALL QUERIES)
        $query->leftJoin('user.profession', 'profession');  //should join dynamically (NOT USEFUL FOR ALL QUERIES)
        $query = $this->dbRepository->applyFilters($filters, $query);
        $query = $this->dbRepository->applyCategories($combo, $query);
        return $query->getQuery()->getArrayResult();
    }

    public function getStartingNumber($from, Combination $combo = null, Filters $filters = null){
        $newFilters = clone $filters;
        $temp = clone $newFilters->getDate()['period']; //error key 'period' is not there
        $temp->setFrom(null);
        $temp->setTo($from->format('Y-m-d'));
        $newFilters->addDate('period', $temp);

        $query = $this->dbRepository->createQueryBuilder('user')->select('COUNT(user)')
            ->orderBy('user.createdtime', 'DESC');
        $query->leftJoin('user.account', 'account');        //should join dynamically (NOT USEFUL FOR ALL QUERIES)
        $query->leftJoin('user.profession', 'profession');  //should join dynamically (NOT USEFUL FOR ALL QUERIES)
        $query = $this->dbRepository->applyFilters($newFilters, $query);
        $query = $this->dbRepository->applyCategories($combo, $query);
        return $query->getQuery()->getSingleScalarResult();
    }

    public function getFooter(int $currentlyShowing){
        $totalUsers = $this->dbRepository->createQueryBuilder('user')->select('COUNT(user)')->getQuery()->getSingleScalarResult();
        return "Total Users: " . $totalUsers . " / Currently showing: " . $currentlyShowing;
    }

    public function getGeneralName(){
        return $this->generalName;
    }

    public function getUnit(){
        return $this->unit;
    }

}