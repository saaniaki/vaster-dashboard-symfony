<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 2017-06-06
 * Time: 11:14 AM
 */

namespace VasterBundle\Repository;


use AppBundle\Module\Combination;
use AppBundle\Module\Configuration\DateRange;
use AppBundle\Module\Configuration\Filters;
use AppBundle\Module\Configuration\Search;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
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
     * @param $combo Combination
     * @param $query
     * @return QueryBuilder
     */
    public function applyCategories($combo, $query){
        $query = $this->count($combo->getUserType(), $combo->isAvailability(), $combo->getDeviceType(), $combo->getSearch(), $combo->getDate(), $query);
        return $query;
    }

    public function applyFilters(Filters $filters = null, $query){
        if ( $filters == null ) return $query;
        else return $this->filter($filters->getUserType(), $filters->getAvailability(), $filters->getDeviceType(), $filters->getSearch(), $filters->getDate(), $query);
    }

    /**
     * Modifies a query results
     * @param mixed $types array idiot
     * @param boolean $availability
     * @param mixed $devices array idiot
     * @param string|null $searches
     * @param mixed $dates
     * @param QueryBuilder $query
     *
     * @return QueryBuilder
     */
    public function filter($types = null, bool $availability = null, $devices = null, $searches = null, $dates = null, QueryBuilder $query){
        //dump($types);
        /* and ( type1 or type2 ) */
        if($types != null){
            $ors = [];
            foreach ($types as $key => $type) {
                $temp = $query->expr()->eq('user.accounttype', ':filter_type' . $key);
                $query->setParameter('filter_type' . $key, $type);
                $ors[] = $temp;
            }
            $temp = $query->expr();
            $query->andWhere(call_user_func_array([$temp, "orX"], $ors));
        }

        if($availability != null) $query->andWhere('profession.available = :filter_availability')->setParameter('filter_availability', $availability);

        /* and ( device1 or device 2 ) */
        if($devices != null){
            $ors = [];
            foreach ($devices as $key => $device) {
                $temp = $query->expr()->eq('account.devicetype', ':filter_device' . $key);
                $query->setParameter('filter_device' . $key, $device);
                $ors[] = $temp;
            }
            $temp = $query->expr();
            $query->andWhere(call_user_func_array([$temp, "orX"], $ors));
        }


        if($searches != null) {
            $outer = [];
            /** @var Search $search */
            foreach ($searches as $key => $search) {
                $expressions = [];
                foreach ( $search->getColumns() as $column ){

                    if( $search->isNegate() ){
                        $temp = $query->expr()->notLike($column, ':filter_value' . $key);

                        if( $search->getColumnOperator() == 'and' ) $search->setColumnOperator('or');
                        else if( $search->getColumnOperator() == 'or' ) $search->setColumnOperator('and');

                        if( $search->getExpressionOperator() == 'and' ) $search->setExpressionOperator('or');
                        else if( $search->getExpressionOperator() == 'or' ) $search->setExpressionOperator('and');

                    }
                    else $temp = $query->expr()->like($column, ':filter_value' . $key);

                    $query->setParameter('filter_value' . $key, $search->getKeyword());
                    $expressions[] = $temp;
                }

                $outer[] = ['expression' => call_user_func_array([$query->expr(), $search->getColumnOperator() . "X"], $expressions), 'operator' => $search->getExpressionOperator()]; //expressionOperator columnOperator
            }

            $full = null;
            $temp = null;

            if( count($outer) == 1 ){
                $full = $outer[0]['expression'];
            } else {
                foreach ($outer as $expression){

                    if( $outer[0] != $expression){
                        $full = $temp->add($expression['expression']);
                        $temp = null;

                        if($expression['operator'] == 'and') $temp = $query->expr()->andX($full);
                        else if($expression['operator'] == 'or') $temp = $query->expr()->orX($full);
                    } else {
                        if($expression['operator'] == 'and') $temp = $query->expr()->andX($expression['expression']);
                        else if($expression['operator'] == 'or') $temp = $query->expr()->orX($expression['expression']);
                    }

                }
            }

            $query->andWhere($full);
        }


        if($dates != null) {
            $full = null;
            $temp = null;
            $expressions = new ArrayCollection();

            /** @var DateRange $date */
            foreach ($dates as $key => $date) {
                //$date = (array) $date;

                if( $date->isNegate() ){ //switch from and to, remove equal
                    $temp = $query->expr()->orX(
                        $query->expr()->gt($date->getColumn(), ':filter_dateTo' . $key),
                        $query->expr()->lt($date->getColumn(), ':filter_dateFrom' . $key)
                    );
                }else {
                    $temp = $query->expr()->andX(
                        $query->expr()->gte($date->getColumn(), ':filter_dateFrom' . $key),
                        $query->expr()->lte($date->getColumn(), ':filter_dateTo' . $key)
                    );
                }

                $adjustedDates = $this->adjustDate($date->getFrom(), $date->getTo());
                $query->setParameter('filter_dateFrom' . $key, $adjustedDates['from']);
                $query->setParameter('filter_dateTo' . $key, $adjustedDates['to']);


                $expressions->add(['expression' => $temp, 'operator' => $date->getOperator()]);
            }

            //dump($expressions);die();

            $full = null;
            $temp = null;

            if (count($expressions) == 1) {
                $full = $expressions[0]['expression'];
            } else {
                foreach ($expressions as $expression) {

                    if ($expressions[0] != $expression) {
                        $full = $temp->add($expression['expression']);
                        $temp = null;

                        if ($expression['operator'] == 'and') $temp = $query->expr()->andX($full);
                        else if ($expression['operator'] == 'or') $temp = $query->expr()->orX($full);
                    } else {
                        if ($expression['operator'] == 'and') $temp = $query->expr()->andX($expression['expression']);
                        else if ($expression['operator'] == 'or') $temp = $query->expr()->orX($expression['expression']);
                    }

                }
            }

            $query->andWhere($full);
        }

        return $query;
    }

    /**
     * Modifies a query results
     * @param string|null $type
     * @param boolean $availability
     * @param mixed $devices
     * @param string|null $searches
     * @param mixed $dates
     * @param QueryBuilder $query
     * @return QueryBuilder
     */
    public function count(string $type = null, bool $availability = null, $devices = null, $searches = null, $dates = null, QueryBuilder $query){
        //dump($type);

        if($type != null) $query->andWhere('user.accounttype = :type')->setParameter('type', $type);
        if($availability !== null) $query->andWhere('profession.available = :availability')->setParameter('availability', $availability);

        /* and ( device1 or device 2 ) */
        if($devices != null){
            $ors = [];
            foreach ($devices as $key => $device) {
                $temp = $query->expr()->eq('account.devicetype', ':device' . $key);
                $query->setParameter('device' . $key, $device);
                $ors[] = $temp;
            }
            $temp = $query->expr();
            $query->andWhere(call_user_func_array([$temp, "orX"], $ors));
        }


        if($searches != null) {

            $outer = [];
            foreach ($searches as $key => $search) {
                $search = (array) $search;

                $expressions = [];
                foreach ( $search['columns'] as $column ){

                    if( $search['negate'] ) $temp = $query->expr()->notLike($column, ':value' . $key);
                    else $temp = $query->expr()->like($column, ':value' . $key);

                    $query->setParameter('value' . $key, $search['keyword']);
                    $expressions[] = $temp;
                }

                if( $search['negate'] ) {

                    if( $search['columnOperator'] == 'and' ) $search['columnOperator'] = 'or';
                    else if( $search['columnOperator'] == 'or' ) $search['columnOperator'] = 'and';


                    $search['expressionOperator'] = 'and'; //overriding null, this is a category not a filter
                    //if( $search['expressionOperator'] == 'and' ) $search['expressionOperator'] = 'or';
                    //else if( $search['expressionOperator'] == 'or' ) $search['expressionOperator'] = 'and';
                }

                $temp = $query->expr();
                $outer[] = ['expression' => call_user_func_array([$temp, $search['columnOperator'] . "X"], $expressions), 'operator' => $search['expressionOperator']]; //expressionOperator columnOperator
            }

            $full = null;
            $temp = null;

            if( count($outer) == 1 ){
                $full = $outer[0]['expression'];
            } else {
                foreach ($outer as $expression){

                    if( $outer[0] != $expression){
                        $full = $temp->add($expression['expression']);
                        $temp = null;

                        if($expression['operator'] == 'and') $temp = $query->expr()->andX($full);
                        else if($expression['operator'] == 'or') $temp = $query->expr()->orX($full);
                    } else {
                        if($expression['operator'] == 'and') $temp = $query->expr()->andX($expression['expression']);
                        else if($expression['operator'] == 'or') $temp = $query->expr()->orX($expression['expression']);
                    }

                }
            }

            $query->andWhere($full);
        }


        if($dates != null) {
            $full = null;
            $temp = null;
            $expressions = new ArrayCollection();

            foreach ($dates as $key => $date) {
                $date = (array) $date;

                if( $date['negate'] ){ //switch from and to, remove equal
                    $temp = $query->expr()->orX(
                        $query->expr()->gt($date['column'], ':dateTo' . $key),
                        $query->expr()->lt($date['column'], ':dateFrom' . $key)
                    );

                    $date['operator'] = 'and'; //overriding null, this is a category not a filter
                    //if( $date['operator'] == 'and' ) $date['operator'] = 'or';
                    //else if( $date['operator'] == 'or' ) $date['operator'] = 'and';
                }else {
                    $temp = $query->expr()->andX(
                        $query->expr()->gte($date['column'], ':dateFrom' . $key),
                        $query->expr()->lte($date['column'], ':dateTo' . $key)
                    );
                }


                $adjustedDates = $this->adjustDate($date['from'], $date['to']);
                $query->setParameter('dateFrom' . $key, $adjustedDates['from']);
                $query->setParameter('dateTo' . $key, $adjustedDates['to']);



                $expressions->add(['expression' => $temp, 'operator' => $date['operator']]);
            }

            //dump($expressions);die();

            $full = null;
            $temp = null;

            if (count($expressions) == 1) {
                $full = $expressions[0]['expression'];
            } else {
                foreach ($expressions as $expression) {

                    if ($expressions[0] != $expression) {
                        $full = $temp->add($expression['expression']);
                        $temp = null;

                        if ($expression['operator'] == 'and') $temp = $query->expr()->andX($full);
                        else if ($expression['operator'] == 'or') $temp = $query->expr()->orX($full);
                    } else {
                        if ($expression['operator'] == 'and') $temp = $query->expr()->andX($expression['expression']);
                        else if ($expression['operator'] == 'or') $temp = $query->expr()->orX($expression['expression']);
                    }

                }
            }

            //dump($full);die();

            $query->andWhere($full); //put it in ()
        }

        return $query;
    }


    /**
     * Counts users who match the configuration
     * @param $filters
     * @return int
     * @internal param mixed $types
     * @internal param bool $availability
     * @internal param mixed $devices
     * @internal param null|string $searches
     * @internal param mixed $dates
     *
     */
    public function generalCount(Filters $filters = null){
        $query = $this->createQueryBuilder('user')->select('COUNT(user)');
        $query->leftJoin('user.account', 'account');        //should join dynamically (NOT USEFUL FOR ALL QUERIES)
        $query->leftJoin('user.profession', 'profession');  //should join dynamically (NOT USEFUL FOR ALL QUERIES)
        $query = $this->applyFilters($filters, $query);
        return $query->getQuery()->getSingleScalarResult();
    }

    public function adjustDate($fromDate, $toDate){
        $yesterday = new \DateTime('2000-01-01');
        $aWeekAgo = new \DateTime('2000-01-07');
        $aMonthAgo = new \DateTime('2000-02-01');


        if( $fromDate == null ) $fromDate = new \DateTime('2016-12-09');
        else $fromDate = new \DateTime($fromDate);

        if( $fromDate == $yesterday ) $fromDate = new \DateTime('midnight yesterday');
        elseif ( $fromDate == $aWeekAgo ) $fromDate = new \DateTime('midnight last week');
        elseif ( $fromDate == $aMonthAgo ) $fromDate = new \DateTime('midnight last month');
        //elseif( $fromDate == null ) $fromDate = new \DateTime('2016-12-09');


        if( $toDate == null ) $toDate = new \DateTime('now');
        else $toDate = new \DateTime($toDate);

        if( $toDate == $yesterday ) $toDate = new \DateTime('midnight yesterday');
        elseif ( $toDate == $aWeekAgo ) $toDate = new \DateTime('midnight last week');
        elseif ( $toDate == $aMonthAgo ) $toDate = new \DateTime('midnight last month');
        //elseif( $toDate == null ) $toDate = new \DateTime('now');


        return ['from' => $fromDate, 'to' => $toDate];
    }


}