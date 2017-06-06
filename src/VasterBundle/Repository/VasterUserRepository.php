<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 2017-06-06
 * Time: 11:14 AM
 */

namespace VasterBundle\Repository;


use Doctrine\ORM\EntityRepository;
use VasterBundle\Entity\User;

class VasterUserRepository extends EntityRepository
{
    /**
     * @param $email
     * @return User|null
     */
    public function findUserID($email){
        return $this->createQueryBuilder('user')
            ->andWhere('user.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }
}