<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 2017-06-20
 * Time: 1:58 PM
 */

namespace VasterBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use VasterBundle\Entity\User;


class VasterSearchRepository extends EntityRepository
{
    function findHistory(User $user){
        return $this->createQueryBuilder('search')
            ->leftJoin('search.user', 'user')
            ->andWhere('user.userid = :id')
            ->setParameter('id', $user->getUserId())
            ->orderBy('search.createdtime', 'DESC')
            ->setMaxResults(50)
            ->getQuery()
            ->getArrayResult();
    }
}