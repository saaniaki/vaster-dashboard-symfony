<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 2017-06-13
 * Time: 5:54 PM
 */

namespace VasterBundle\Repository;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use VasterBundle\Entity\User;

class VasterProfessionRepository extends EntityRepository
{
    /**
     * @return integer
     */
    public function count($type, $keyword = null){
        if($keyword == null) {
            if($type == 'all'){
                return $this->createQueryBuilder('profession')
                    ->leftJoin('profession.user', 'user')
                    ->andWhere('profession.available = 1')
                    ->select('COUNT(profession.available)')
                    ->getQuery()
                    ->getSingleScalarResult();
            }else{
                return $this->createQueryBuilder('profession')
                    ->leftJoin('profession.user', 'user')
                    ->andWhere('user.accounttype = :type')
                    ->setParameter('type', $type)
                    ->andWhere('profession.available = 1')
                    ->select('COUNT(profession.available)')
                    ->getQuery()
                    ->getSingleScalarResult();
            }
        }else if($type == 'all'){
            return $this->createQueryBuilder('profession')
                ->leftJoin('profession.user', 'user')
                ->leftJoin('user.account', 'account')
                ->leftJoin('user.lastseen', 'lastseen')
                ->where('profession.available = 1')
                ->andWhere('user.firstname LIKE :keyword')
                ->orWhere('user.lastname LIKE :keyword')
                ->orWhere('user.email LIKE :keyword')
                ->orWhere('user.phone LIKE :keyword')
                ->setParameter('keyword', $keyword)
                ->select('COUNT(profession.available)')
                ->getQuery()
                ->getSingleScalarResult();
        }else{
            return $this->createQueryBuilder('profession')
                ->leftJoin('profession.user', 'user')
                ->leftJoin('user.account', 'account')
                ->leftJoin('user.lastseen', 'lastseen')
                ->where('profession.available = 1')
                ->andWhere('user.accounttype = :type')
                ->andWhere('user.firstname LIKE :keyword')
                ->orWhere('user.lastname LIKE :keyword')
                ->orWhere('user.email LIKE :keyword')
                ->orWhere('user.phone LIKE :keyword')
                ->setParameter('type', $type)
                ->setParameter('keyword', $keyword)
                ->select('COUNT(profession.available)')
                ->getQuery()
                ->getSingleScalarResult();
        }
    }
}