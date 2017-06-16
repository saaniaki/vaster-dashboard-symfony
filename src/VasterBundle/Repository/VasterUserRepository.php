<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 2017-06-06
 * Time: 11:14 AM
 */

namespace VasterBundle\Repository;


use Doctrine\Common\Collections\ArrayCollection;
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

    /**
     * @param int $limit
     * @param int $offset
     * @param string $sort
     * @param string|null $order
     * @return User[]|null
     */
    public function showPage(int $limit, int $offset, string $sort = 'user.userid', string $order = null, $type = 'all'){
        if( $type == 'all' ){
            return $this->createQueryBuilder('user')
                ->leftJoin('user.account', 'account')
                ->leftJoin('user.profession', 'profession')
                ->leftJoin('user.lastseen', 'lastseen')
                ->orderBy($sort, $order)
                ->setMaxResults($limit)
                ->setFirstResult($offset)
                ->getQuery()
                ->execute();
        } else {
            return $this->createQueryBuilder('user')
                ->andWhere('user.accounttype != :type')
                ->setParameter('type', $type)
                ->leftJoin('user.account', 'account')
                ->leftJoin('user.profession', 'profession')
                ->leftJoin('user.lastseen', 'lastseen')
                ->orderBy($sort, $order)
                ->setMaxResults($limit)
                ->setFirstResult($offset)
                ->getQuery()
                ->execute();
        }
    }

    /**
     * @param int $limit
     * @param int $offset
     * @param string $sort
     * @param string|null $order
     * @return User[]|null
     */
    public function showPageExclude(int $limit, int $offset, string $sort = 'user.userid', string $order = null, $type){
        return $this->createQueryBuilder('user')
            ->andWhere('user.accounttype != :type')
            ->setParameter('type', $type)
            ->leftJoin('user.account', 'account')
            ->leftJoin('user.profession', 'profession')
            ->leftJoin('user.lastseen', 'lastseen')
            ->orderBy($sort, $order)
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->execute();
    }


    /**
     * @param int $limit
     * @param int $offset
     * @param string $sort
     * @param string|null $order
     * @return User[]|null
     */
    public function showPageSearch(int $limit, int $offset, string $sort = 'user.userid', string $order = null, $type, $keyword){
        if($type == 'all'){
            return $this->createQueryBuilder('user')
                ->leftJoin('user.account', 'account')
                ->leftJoin('user.profession', 'profession')
                ->leftJoin('user.lastseen', 'lastseen')
                ->andWhere('user.firstname LIKE :keyword or user.lastname LIKE :keyword or user.email LIKE :keyword or user.phone LIKE :keyword')
                ->setParameter('keyword', $keyword)
                ->orderBy($sort, $order)
                ->setMaxResults($limit)
                ->setFirstResult($offset)
                ->getQuery()
                ->execute();
        }else{
            return $this->createQueryBuilder('user')
                ->leftJoin('user.account', 'account')
                ->leftJoin('user.profession', 'profession')
                ->leftJoin('user.lastseen', 'lastseen')
                ->andwhere('user.accounttype = :type')
                ->andWhere('user.firstname LIKE :keyword or user.lastname LIKE :keyword or user.email LIKE :keyword or user.phone LIKE :keyword')
                ->setParameter('type', $type)
                ->setParameter('keyword', $keyword)
                ->orderBy($sort, $order)
                ->setMaxResults($limit)
                ->setFirstResult($offset)
                ->getQuery()
                ->execute();
        }
    }

    /**
     * @param int $limit
     * @param int $offset
     * @param string $sort
     * @param string|null $order
     * @return User[]|null
     */
    public function showPageSearchExclude(int $limit, int $offset, string $sort = 'user.userid', string $order = null, $type, $keyword){
        return $this->createQueryBuilder('user')
            ->leftJoin('user.account', 'account')
            ->leftJoin('user.profession', 'profession')
            ->leftJoin('user.lastseen', 'lastseen')
            ->andwhere('user.accounttype != :type')
            ->andWhere('user.firstname LIKE :keyword or user.lastname LIKE :keyword or user.email LIKE :keyword or user.phone LIKE :keyword')
            ->setParameter('type', $type)
            ->setParameter('keyword', $keyword)
            ->orderBy($sort, $order)
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->execute();
    }

    /**
     * @return integer
     */
    public function count($type, $keyword = null){
        if($keyword == null){
            if($type == 'all'){
                return $this->createQueryBuilder('user')
                    ->select('COUNT(user)')
                    ->getQuery()
                    ->getSingleScalarResult();
            }else{
                return $this->createQueryBuilder('user')
                    ->andWhere('user.accounttype = :type')
                    ->setParameter('type', $type)
                    ->select('COUNT(user)')
                    ->getQuery()
                    ->getSingleScalarResult();
            }
        }else if($type == 'all'){
            return $this->createQueryBuilder('user')
                ->leftJoin('user.account', 'account')
                ->leftJoin('user.profession', 'profession')
                ->leftJoin('user.lastseen', 'lastseen')
                ->andWhere('user.firstname LIKE :keyword or user.lastname LIKE :keyword or user.email LIKE :keyword or user.phone LIKE :keyword')
                ->setParameter('keyword', $keyword)
                ->select('COUNT(account.devicetype)') //waht?!
                ->getQuery()
                ->getSingleScalarResult();
        }else{
            return $this->createQueryBuilder('user')
                ->leftJoin('user.account', 'account')
                ->leftJoin('user.profession', 'profession')
                ->leftJoin('user.lastseen', 'lastseen')
                ->andWhere('user.accounttype = :type')
                ->andWhere('user.firstname LIKE :keyword or user.lastname LIKE :keyword or user.email LIKE :keyword or user.phone LIKE :keyword')
                ->setParameter('type', $type)
                ->setParameter('keyword', $keyword)
                ->select('COUNT(account.devicetype)') //what?!
                ->getQuery()
                ->getSingleScalarResult();
        }
    }

    /**
     * @return integer
     */
    public function countProfession($type, $keyword = null){
        if($keyword == null) {
            if($type == 'all'){
                return $this->createQueryBuilder('user')
                    ->leftJoin('user.profession', 'profession')
                    ->andWhere('profession.available = 1')
                    ->select('COUNT(profession.available)')
                    ->getQuery()
                    ->getSingleScalarResult();
            }else{
                return $this->createQueryBuilder('user')
                    ->leftJoin('user.profession', 'profession')
                    ->andWhere('user.accounttype = :type')
                    ->setParameter('type', $type)
                    ->andWhere('profession.available = 1')
                    ->select('COUNT(profession.available)')
                    ->getQuery()
                    ->getSingleScalarResult();
            }
        }else if($type == 'all'){
            return $this->createQueryBuilder('user')
                ->leftJoin('user.profession', 'profession')
                ->andWhere('profession.available = 1')
                ->andWhere('user.firstname LIKE :keyword or user.lastname LIKE :keyword or user.email LIKE :keyword or user.phone LIKE :keyword')
                ->setParameter('keyword', $keyword)
                ->select('COUNT(profession.available)')
                ->getQuery()
                ->getSingleScalarResult();
        }else{
            return $this->createQueryBuilder('user')
                ->leftJoin('user.profession', 'profession')
                ->andWhere('profession.available = 1')
                ->andWhere('user.accounttype = :type')
                ->andWhere('user.firstname LIKE :keyword or user.lastname LIKE :keyword or user.email LIKE :keyword or user.phone LIKE :keyword')
                ->setParameter('type', $type)
                ->setParameter('keyword', $keyword)
                ->select('COUNT(profession.available)')
                ->getQuery()
                ->getSingleScalarResult();
        }
    }

    /**
     * @return integer
     */
    public function countAccount($type, $device, $keyword = null){
        if($keyword == null) {
            if($type == 'all'){
                return $this->createQueryBuilder('user')
                    ->leftJoin('user.account', 'account')
                    ->where('account.devicetype = :device')
                    ->setParameter('device', $device)
                    ->select('COUNT(account.devicetype)')
                    ->getQuery()
                    ->getSingleScalarResult();
            }else{
                return $this->createQueryBuilder('user')
                    ->leftJoin('user.account', 'account')
                    ->andWhere('user.accounttype = :type')
                    ->andWhere('account.devicetype = :device')
                    ->setParameter('type', $type)
                    ->setParameter('device', $device)
                    ->select('COUNT(account.devicetype)')
                    ->getQuery()
                    ->getSingleScalarResult();
            }
        }else if($type == 'all'){
            return $this->createQueryBuilder('user')
                ->leftJoin('user.account', 'account')
                ->andwhere('account.devicetype = :device')
                ->andWhere('user.firstname LIKE :keyword or user.lastname LIKE :keyword or user.email LIKE :keyword or user.phone LIKE :keyword')
                ->setParameter('keyword', $keyword)
                ->setParameter('device', $device)
                ->select('COUNT(account.devicetype)')
                ->getQuery()
                ->getSingleScalarResult();
        }else{
            return $this->createQueryBuilder('user')
                ->leftJoin('user.account', 'account')
                ->andwhere('account.devicetype = :device')
                ->andWhere('user.accounttype = :type')
                ->andWhere('user.firstname LIKE :keyword or user.lastname LIKE :keyword or user.email LIKE :keyword or user.phone LIKE :keyword')
                ->setParameter('type', $type)
                ->setParameter('keyword', $keyword)
                ->setParameter('device', $device)
                ->select('COUNT(account.devicetype)')
                ->getQuery()
                ->getSingleScalarResult();
        }
    }

}