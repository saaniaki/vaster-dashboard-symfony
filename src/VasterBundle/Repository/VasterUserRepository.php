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
    public function count($type, $keyword = null, \DateTime $from = null,\DateTime $to = null){
        $query = $this->createQueryBuilder('user')
            ->select('COUNT(user)');

        if($type != 'all')
            $query->andWhere('user.accounttype = :type')
                ->setParameter('type', $type);

        if($keyword != null)
            $query->andWhere('user.firstname LIKE :keyword or user.lastname LIKE :keyword or user.email LIKE :keyword or user.phone LIKE :keyword')
                ->setParameter('keyword', $keyword);

        if($from != null){
            $query->andWhere('user.createdtime > :from')
                ->setParameter('from', $from);
        }

        if($to != null){
            $query->andWhere('user.createdtime < :to')
                ->setParameter('to', $to);
        }

        return $query->getQuery()
            ->getSingleScalarResult();



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


    /**
     * @return integer
     */
    public function countProfessionAccount($type, $device, $keyword = null){
        if($keyword == null) {
            if($type == 'all'){
                return $this->createQueryBuilder('user')
                    ->leftJoin('user.profession', 'profession')
                    ->leftJoin('user.account', 'account')
                    ->andWhere('profession.available = 1')
                    ->andwhere('account.devicetype = :device')
                    ->setParameter('device', $device)
                    ->select('COUNT(profession.available)')
                    ->getQuery()
                    ->getSingleScalarResult();
            }else{
                return $this->createQueryBuilder('user')
                    ->leftJoin('user.profession', 'profession')
                    ->leftJoin('user.account', 'account')
                    ->andWhere('user.accounttype = :type')
                    ->andwhere('account.devicetype = :device')
                    ->setParameter('type', $type)
                    ->setParameter('device', $device)
                    ->andWhere('profession.available = 1')
                    ->select('COUNT(profession.available)')
                    ->getQuery()
                    ->getSingleScalarResult();
            }
        }else if($type == 'all'){
            return $this->createQueryBuilder('user')
                ->leftJoin('user.profession', 'profession')
                ->leftJoin('user.account', 'account')
                ->andWhere('profession.available = 1')
                ->andwhere('account.devicetype = :device')
                ->andWhere('user.firstname LIKE :keyword or user.lastname LIKE :keyword or user.email LIKE :keyword or user.phone LIKE :keyword')
                ->setParameter('device', $device)
                ->setParameter('keyword', $keyword)
                ->select('COUNT(profession.available)')
                ->getQuery()
                ->getSingleScalarResult();
        }else{
            return $this->createQueryBuilder('user')
                ->leftJoin('user.profession', 'profession')
                ->leftJoin('user.account', 'account')
                ->andWhere('profession.available = 1')
                ->andwhere('account.devicetype = :device')
                ->andWhere('user.accounttype = :type')
                ->andWhere('user.firstname LIKE :keyword or user.lastname LIKE :keyword or user.email LIKE :keyword or user.phone LIKE :keyword')
                ->setParameter('device', $device)
                ->setParameter('type', $type)
                ->setParameter('keyword', $keyword)
                ->select('COUNT(profession.available)')
                ->getQuery()
                ->getSingleScalarResult();
        }
    }

    public function registrationNumber($type, $keyword, \DateTime $from,\DateTime $to,\DateInterval $interval)
    {
        // if from > to throw expection
        // from / inreval should reach to

        $from = clone $from;
        $to = clone $to;

        $column = $this->createQueryBuilder('user')
            ->select('user.createdtime, user.accounttype')
            ->orderBy('user.createdtime', 'DESC');
        if($keyword != null) {
            $column = $column->andWhere('user.firstname LIKE :keyword or user.lastname LIKE :keyword or user.email LIKE :keyword or user.phone LIKE :keyword')
                ->setParameter('keyword', $keyword);
        }
        if($type != 'all') {
            $column = $column->andWhere('user.accounttype = :type')
                ->setParameter('type', $type);
        }
        $column = $column->getQuery()
            ->getArrayResult();


        $count = 0;
        $result = [];
        while( !($from > $to) ){
            $intervalEnd = clone $from;
            $intervalEnd->add($interval);

            $number = 0;
            /** @var $item \DateTime */
            foreach ( $column as $item ){
                if( $from < $item['createdtime'] && $item['createdtime'] < $intervalEnd ){
                    array_pop($column);
                    $number++;
                }
            }

            $result[$count] = [
                'from' => clone $from,
                'to' => $intervalEnd,
                'number' => $number
            ];
            $count++;
            $from->add($interval);
        }

        return $result;
    }

}