<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 30/08/17
 * Time: 2:17 PM
 */

namespace AppBundle\Module\Graph\SubModule;


use AppBundle\Module\Combination;
use AppBundle\Module\Configuration\Filters;
use AppBundle\Module\ModuleInterface;
use AppBundle\Module\SubModuleInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;

class LastSeen implements SubModuleInterface
{
    private $generalName = "All LastSeens";
    private $unit = "LastSeens";
    private $dbRepository;
    private $userRep;

    public function __construct(ModuleInterface $vdpModule, ObjectManager $em)
    {
        $this->dbRepository = $em->getRepository("VasterBundle:LastSeen");
        $this->userRep = $em->getRepository("VasterBundle:User");

        $vdpModule->setTitle('LastSeen Number');
        $vdpModule->yTitle = 'LastSeen';
        $vdpModule->y1Title = 'LastSeen Cumulative';
        $vdpModule->color = new ArrayCollection(['#c42525', '#0d233a', '#f28f43', '#8bbc21', '#1aadce', '#492970', '#910000', '#77a1e5', '#2f7ed8', '#a6c96a']);
    }

    public function count(Combination $combo = null, Filters $filters = null){
        $query = $this->dbRepository->createQueryBuilder('lastSeen')
            ->leftJoin('lastSeen.user', 'user')
            ->select('COUNT(user)');
        $query->leftJoin('user.account', 'account');        //should join dynamically (NOT USEFUL FOR ALL QUERIES)
        $query->leftJoin('user.profession', 'profession');  //should join dynamically (NOT USEFUL FOR ALL QUERIES)
        $query = $this->userRep->applyFilters($filters, $query);
        $query = $this->userRep->applyCategories($combo, $query);
        return $query->getQuery()->getSingleScalarResult();
    }

    public function getColumn(Combination $combo = null, Filters $filters = null){
        $query = $this->dbRepository->createQueryBuilder('lastSeen')
            ->leftJoin('lastSeen.user', 'user')
            ->select('lastSeen.seconds as time')
            ->orderBy('lastSeen.seconds', 'DESC');
        $query->leftJoin('user.account', 'account');        //should join dynamically (NOT USEFUL FOR ALL QUERIES)
        $query->leftJoin('user.profession', 'profession');  //should join dynamically (NOT USEFUL FOR ALL QUERIES)
        $query = $this->userRep->applyFilters($filters, $query);
        $query = $this->userRep->applyCategories($combo, $query);

        $data = $query->getQuery()->getArrayResult();

        foreach ( $data as &$row ){
            $dateObj = new \DateTime();
            $dateObj->setTimestamp($row['time']);
            $row['time'] = $dateObj;
        }

        return $data;
    }

    public function getStartingNumber($from, Combination $combo = null, Filters $filters = null){
        $newFilters = clone $filters;
        $temp = clone $newFilters->getDate()['period']; //error key 'period' is not there
        $temp->setFrom(null);
        $temp->setTo($from->format('Y-m-d'));
        $newFilters->addDate('period', $temp);

        $query = $this->dbRepository->createQueryBuilder('lastSeen')
            ->leftJoin('lastSeen.user', 'user')
            ->select('COUNT(user)')
            ->orderBy('lastSeen.seconds', 'DESC');
        $query->leftJoin('user.account', 'account');        //should join dynamically (NOT USEFUL FOR ALL QUERIES)
        $query->leftJoin('user.profession', 'profession');  //should join dynamically (NOT USEFUL FOR ALL QUERIES)
        $query = $this->userRep->applyFilters($newFilters, $query);
        $query = $this->userRep->applyCategories($combo, $query);
        return $query->getQuery()->getSingleScalarResult();
    }

    public function getFooter(int $currentlyShowing){
        $totalUsers = $this->dbRepository->createQueryBuilder('lastSeen')->leftJoin('lastSeen.user', 'user')->select('COUNT(user)')
            ->getQuery()->getSingleScalarResult();
        return "Total LastSeens: " . $totalUsers . " / Currently showing: " . $currentlyShowing;
    }

    public function getGeneralName(){
        return $this->generalName;
    }

    public function getUnit(){
        return $this->unit;
    }

}