<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 2017-08-20
 * Time: 10:02 AM
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


class Search implements SubModuleInterface
{
    private $generalName = "All Searches";
    private $unit = "Searches";
    private $dbRepository;
    private $userRep;

    public function __construct(ModuleInterface $vdpModule, ObjectManager $em)
    {
        $this->dbRepository = $em->getRepository("VasterBundle:Search");
        $this->userRep = $em->getRepository("VasterBundle:User");

        $vdpModule->setTitle('Search Number Over Time');
        $vdpModule->yTitle = 'Search';
        $vdpModule->y1Title = 'Search Cumulative';
        $vdpModule->color = new ArrayCollection(['#c42525', '#0d233a', '#f28f43', '#8bbc21', '#1aadce', '#492970', '#910000', '#77a1e5', '#2f7ed8', '#a6c96a']);
    }

    public function count(Combination $combo = null, Filters $filters = null){
        $query = $this->dbRepository->createQueryBuilder('searches')
            ->leftJoin('searches.user', 'user')
            ->select('COUNT(user)')
            ->orderBy('searches.createdtime', 'DESC');
        $query->leftJoin('user.account', 'account');        //should join dynamically (NOT USEFUL FOR ALL QUERIES)
        $query->leftJoin('user.profession', 'profession');  //should join dynamically (NOT USEFUL FOR ALL QUERIES)
        $query->leftJoin('user.lastseen', 'lastSeen');  //should join dynamically (NOT USEFUL FOR ALL QUERIES)
        $query = $this->userRep->applyFilters($filters, $query);
        $query = $this->userRep->applyCategories($combo, $query);
        return $query->getQuery()->getSingleScalarResult();
    }

    public function getColumn(Combination $combo = null, Filters $filters = null){
        $query = $this->dbRepository->createQueryBuilder('searches')
            ->join('searches.user', 'user')
            ->select('searches.createdtime as time')
            ->orderBy('searches.createdtime', 'DESC');
        $query->leftJoin('user.account', 'account');        //should join dynamically (NOT USEFUL FOR ALL QUERIES)
        $query->leftJoin('user.profession', 'profession');  //should join dynamically (NOT USEFUL FOR ALL QUERIES)
        $query->leftJoin('user.lastseen', 'lastSeen');  //should join dynamically (NOT USEFUL FOR ALL QUERIES)
        $query = $this->userRep->applyFilters($filters, $query);
        $query = $this->userRep->applyCategories($combo, $query);
        dump($query->getQuery());
        return $query->getQuery()->getArrayResult();
    }

    public function getStartingNumber($from, Combination $combo = null, Filters $filters = null){
        $newFilters = clone $filters;
        $temp = clone $newFilters->getDate()['period']; //error key 'period' is not there
        $temp->setFrom(null);
        $temp->setTo($from->format('Y-m-d'));
        $newFilters->addDate('period', $temp);

        $query = $this->dbRepository->createQueryBuilder('searches')
            ->leftJoin('searches.user', 'user')
            ->select('COUNT(user)')
            ->orderBy('searches.createdtime', 'DESC');
        $query->leftJoin('user.account', 'account');        //should join dynamically (NOT USEFUL FOR ALL QUERIES)
        $query->leftJoin('user.profession', 'profession');  //should join dynamically (NOT USEFUL FOR ALL QUERIES)
        $query->leftJoin('user.lastseen', 'lastSeen');  //should join dynamically (NOT USEFUL FOR ALL QUERIES)
        $query = $this->userRep->applyFilters($newFilters, $query);
        $query = $this->userRep->applyCategories($combo, $query);
        return $query->getQuery()->getSingleScalarResult();
    }

    public function getFooter(int $currentlyShowing){
        $totalUsers = $this->dbRepository->createQueryBuilder('searches')->leftJoin('searches.user', 'user')->select('COUNT(user)')
            ->getQuery()->getSingleScalarResult();
        return "Total Searches: " . $totalUsers . " / Currently showing: " . $currentlyShowing;
    }

    public function getGeneralName(){
        return $this->generalName;
    }

    public function getUnit(){
        return $this->unit;
    }

}