<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 2017-06-16
 * Time: 1:11 PM
 */

namespace VasterBundle\Repository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use VasterBundle\Entity\User;

class VasterLocationRepository extends EntityRepository
{
    function findValidLocation(User $user){
        $locations = $this->createQueryBuilder('location')
            ->leftJoin('location.user', 'user')
            ->andWhere('user.userid = :id')
            ->setParameter('id', $user->getUserId())
            ->orderBy('location.createdtime', 'DESC')
            ->getQuery()
            ->getArrayResult();

        $result = null;
        foreach ($locations as $location) {
            if( !($location['latitude'] == 0 && $location['longitude'] == 0) && !($location['latitude'] == -180 && $location['longitude'] == -180) ){
                $result = $location;
                break;
            }
        }
        return $result;
    }
}