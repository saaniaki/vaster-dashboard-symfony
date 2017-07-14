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
use Doctrine\ORM\QueryBuilder;
use VasterBundle\Entity\User;
use \DateTime;

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

    //$key => $cat
    public function applyCategories($combo, $query){
        $name = $userType = $deviceType = $availability = $searches = $dates = null;

        foreach( $combo as $key => $cat ){
            if( is_string($cat) ) {
                // $key is the type
                $name .= "/" . $cat;

                if( strtolower($key) == 'user_type' ) $userType = [$cat]; //changed

                if( strtolower($key) == 'availability' ){
                    if( strtolower($cat) == "orange hat" ) $availability = true;
                    else $availability = false;
                }
                if( strtolower($key) == 'device_type' ){ //changed
                    if( strtolower($cat) == "android" ) $deviceType = ["android"];
                    else if( strtolower($cat) == "ios" ) $deviceType = ["iPhone", "iPad"];
                }

            }
            else {
                if( strtolower($cat['type']) == 'search' ) $searches = $cat['value'];

                if( strtolower($cat['type']) == 'date' ) $dates = $cat['value'];

                if( $cat['match'] ) $name .= "/" . $key;
                else {
                    $name .= "/!" . $key;
                    if( strtolower($cat['type']) == 'search' ) foreach ( $searches as $k => $search ) $searches[$k]['negate'] = !$search['negate'];
                    if( strtolower($cat['type']) == 'date' ) foreach ( $dates as $k => $date ) $dates[$k]['negate'] = !$date['negate'];
                }
            }
        }

        $name = substr($name, 1);
        $query = $this->NEWmodifyCount($userType, $availability, $deviceType, $searches, $dates, $query);

        return ['query' => $query, 'name' => $name];
    }

    public function applyFilters($filters, $query){
        $filter_userTypes =  $filter_availability = $filter_deviceTypes = $filter_searches = $filter_dates = null;

        if( isset($filters['user_type']) ){ //changed
            $filter_userTypes = new ArrayCollection($filters['user_type']);//changed
        }

        if( isset($filters['availability']) ){
            $filter_availability = new ArrayCollection($filters['availability']);
            if ($filter_availability->contains(strtolower("Orange Hat")) && !$filter_availability->contains(strtolower("Regular")) )$filter_availability = true;
            else if (!$filter_availability->contains(strtolower("Orange Hat")) && $filter_availability->contains(strtolower("Regular"))) $filter_availability = false;
            else if (!$filter_availability->contains(strtolower("Orange Hat")) && !$filter_availability->contains(strtolower("Regular"))) $filter_availability = null; // this should return error
            else if ($filter_availability->contains(strtolower("Orange Hat")) && $filter_availability->contains(strtolower("Regular"))) $filter_availability = null;
        }

        if( isset($filters['device_type']) ){
            $filter_deviceTypes = [];
            $temp = new ArrayCollection($filters['device_type']);
            if( $temp->contains(strtolower("Android")) ) array_push($filter_deviceTypes, "android");
            if( $temp->contains(strtolower("ios")) ) {
                array_push($filter_deviceTypes, "iPhone");
                array_push($filter_deviceTypes, "iPad");
            }
        }

        if( isset($filters['search']) ){
            $temp = new ArrayCollection($filters['search']);

            foreach ($temp as $item){
                $filter_searches[] = $item[0];
            }


            //dump $filter_searches to make sure
        }
        //dump($filter_searches);die();
        if( isset($filters['date']) ){
            $filter_dates = new ArrayCollection($filters['date']);
            $filter_dates = $filter_dates->first();
            //dump $filter_dates to make sure
            //dump($filter_dates);die();
        }


        return $this->filter($filter_userTypes, $filter_availability, $filter_deviceTypes, $filter_searches, $filter_dates, $query);
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


        /* and ( type1 or type2 ) */
        $ors = []; // put inside
        if($types != null){
            foreach ($types as $key => $type) {
                $temp = $query->expr()->eq('user.accounttype', ':filter_type' . $key);
                $query->setParameter('filter_type' . $key, $type);
                $ors[] = $temp;
            }
            $temp = $query->expr();
            $query->andWhere(call_user_func_array([$temp, "orX"], $ors));
        }


        if($availability !== null) $query->andWhere('profession.available = :filter_availability')->setParameter('filter_availability', $availability);

        /* and ( device1 or device 2 ) */
        $ors = []; // put inside
        if($devices != null){
            //$query->leftJoin('user.account', 'account');
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
            foreach ($searches as $key => $search) {

                $expressions = [];
                foreach ( $search['columns'] as $column ){

                    if( $search['negate'] ) $temp = $query->expr()->notLike($column, ':filter_value' . $key);
                    else $temp = $query->expr()->like($column, ':filter_value' . $key);

                    $query->setParameter('filter_value' . $key, $search['keyword']);
                    $expressions[] = $temp;
                }

                if( $search['negate'] ) {

                    if( $search['columnOperator'] == 'and' ) $search['columnOperator'] = 'or';
                    else if( $search['columnOperator'] == 'or' ) $search['columnOperator'] = 'and';

                    if( $search['expressionOperator'] == 'and' ) $search['expressionOperator'] = 'or';
                    else if( $search['expressionOperator'] == 'or' ) $search['expressionOperator'] = 'and';

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


        //$date['from']
        //$date['to']
        //$date['negate']
        //$date['operator']
        //$date['column']
        if($dates != null) {
            $full = null;
            $temp = null;
            $expressions = new ArrayCollection();

            foreach ($dates as $key => $date) {


                if( $date['negate'] ){ //switch from and to, remove equal
                    $temp = $query->expr()->orX(
                        $query->expr()->gt($date['column'], ':filter_dateTo' . $key),
                        $query->expr()->lt($date['column'], ':filter_dateFrom' . $key)
                    );
                }else {
                    $temp = $query->expr()->andX(
                        $query->expr()->gte($date['column'], ':filter_dateFrom' . $key),
                        $query->expr()->lte($date['column'], ':filter_dateTo' . $key)
                    );
                }

                $adjustedDates = $this->adjustDate($date['from'], $date['to']);
                $query->setParameter('filter_dateFrom' . $key, $adjustedDates['from']);
                $query->setParameter('filter_dateTo' . $key, $adjustedDates['to']);


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

            $query->andWhere($full);
        }

        return $query;
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
    public function NEWmodifyCount($types = null, bool $availability = null, $devices = null, $searches = null, $dates = null, QueryBuilder $query){
        //if($types != null) foreach ($types as $key => $type) $query->orWhere('user.accounttype = :type' . $key)->setParameter('type' . $key, $type);
        /* and ( type1 or type2 ) */
        $ors = []; // put inside
        if($types != null){
            foreach ($types as $key => $type) {
                $temp = $query->expr()->eq('user.accounttype', ':type' . $key);
                $query->setParameter('type' . $key, $type);
                $ors[] = $temp;
            }
            $temp = $query->expr();
            $query->andWhere(call_user_func_array([$temp, "orX"], $ors));
        }






        /* and ( userType1 or userType2 )
        $ors = [];
        if($types != null){
            //$query->leftJoin('user.account', 'account');
            foreach ($types as $key => $type) {
                $temp = $query->expr()->eq('user.accounttype', ':type' . $key);
                $query->setParameter('type' . $key, $type);
                $ors[] = $temp;
            }
            $temp = $query->expr();
            $query->andWhere(call_user_func_array([$temp, "orX"], $ors));
        }*/

        if($availability !== null) $query->andWhere('profession.available = :availability')->setParameter('availability', $availability);

        /* and ( device1 or device 2 ) */
        $ors = []; // put inside
        if($devices != null){

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

                    if( $search['expressionOperator'] == 'and' ) $search['expressionOperator'] = 'or';
                    else if( $search['expressionOperator'] == 'or' ) $search['expressionOperator'] = 'and';

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


        //$date['from']
        //$date['to']
        //$date['negate']
        //$date['operator']
        //$date['column']
        if($dates != null) {
            $full = null;
            $temp = null;
            $expressions = new ArrayCollection();

            foreach ($dates as $key => $date) {


                if( $date['negate'] ){ //switch from and to, remove equal
                    $temp = $query->expr()->orX(
                        $query->expr()->gt($date['column'], ':dateTo' . $key),
                        $query->expr()->lt($date['column'], ':dateFrom' . $key)
                    );

                    if( $date['operator'] == 'and' ) $date['operator'] = 'or';
                    else if( $date['operator'] == 'or' ) $date['operator'] = 'and';
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

    /*
     * Modifies a query results
     * @param mixed $types array idiot
     * @param boolean $availability
     * @param mixed $devices array idiot
     * @param string|null $keyword
     * @param \DateTime|null $from
     * @param \DateTime|null $to
     * @param QueryBuilder $query
     *
     * @return QueryBuilder

    private function modifyCount($types = null, bool $availability = null, $devices = null, $keyword = null, DateTime $from = null, DateTime $to = null, QueryBuilder $query){
        if($types != null) foreach ($types as $key => $type) $query->orWhere('user.accounttype = :type' . $key)->setParameter('type' . $key, $type);
        if($availability !== null) $query->leftJoin('user.profession', 'profession')->andWhere('profession.available = :availability')->setParameter('availability', $availability);

        //if($devices != null) $query->leftJoin('user.account', 'account') ->andwhere('account.devicetype = :device')->setParameter('device', $devices);
        if($devices != null){
            $query->leftJoin('user.account', 'account');
            foreach ($devices as $key => $device) $query->andWhere('account.devicetype = :device' . $key)->setParameter('device' . $key, $device);
        }
        if($keyword != null) $query->andWhere('user.firstname LIKE :keyword or user.lastname LIKE :keyword or user.email LIKE :keyword or user.phone LIKE :keyword')->setParameter('keyword', $keyword);
        if($from != null) $query->andWhere('user.createdtime > :from')->setParameter('from', $from);
        if($to != null) $query->andWhere('user.createdtime < :to')->setParameter('to', $to);
        return $query;
    }*/

    /**
     * Counts users who match the configuration
     * @param mixed $types
     * @param boolean $availability
     * @param mixed $devices
     * @param string|null $searches
     * @param mixed $dates
     *
     * @return integer
     */
    public function generalCount($types = null, bool $availability = null, $devices = [], $searches = null, $dates = null){
        //$query = $this->createQueryBuilder('user')->select('user.userid');
        //dump($this->NEWmodifyCount($types, $availability, $devices, $searches, $dates, $query)->orderBy('user.userid')->getQuery()->getArrayResult());
        $query = $this->createQueryBuilder('user')->select('COUNT(user)');
        return $this->NEWmodifyCount($types, $availability, $devices, $searches, $dates, $query)->getQuery()->getSingleScalarResult();
    }

    /*
     * Counts users
     * @param string $type
     * @param string|null $keyword
     * @param \DateTime|null $from
     * @param \DateTime|null $to
     *
     * @return integer

    public function count(string $type, string $keyword = null, DateTime $from = null, DateTime $to = null){
        $query = $this->createQueryBuilder('user')->select('COUNT(user)');
        return $this->modifyCount($type, null, null, $keyword, $from,$to, $query)
            ->getQuery()
            ->getSingleScalarResult();
    }*/

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