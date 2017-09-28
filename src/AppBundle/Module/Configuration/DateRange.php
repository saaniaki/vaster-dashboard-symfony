<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 13/07/17
 * Time: 5:10 PM
 */

namespace AppBundle\Module\Configuration;


use Doctrine\Common\Collections\ArrayCollection;

class DateRange
{
    /** @var $singleCategories ArrayCollection */
    private static $operators_available;

    /** @var $singleCategories ArrayCollection */
    public static $columns_available = ['user.createdtime' , 'lastSeen.seconds', 'searches.createdtime'];

    public $from;
    public $to;
    public $column;
    public $operator;
    public $negate = false;

    public static $yesterday;
    public static $aWeekAgo;
    public static $aMonthAgo;

    public function __construct()
    {
        if( !isset(self::$operators_available) )
            self::$operators_available = new ArrayCollection(['and', 'or']);

        if( is_array(self::$columns_available) )
            self::$columns_available = new ArrayCollection(self::$columns_available);

        if( !isset(self::$yesterday) ) {
            self::$yesterday = '2000-01-01';
            self::$aWeekAgo = '2000-01-07';
            self::$aMonthAgo = '2000-02-01';
        }

        $this->setFrom('2016-12-09 00:00');
        $this->setColumn('user.createdtime');
        $this->setOperator('or');
    }

    /**
     * @return mixed
     */
    public function getFrom()
    {
        return $this->from;
        /*if ($this->from == self::$yesterday) return 'Yesterday';
        elseif ($this->from == self::$aWeekAgo) return 'A week ago';
        elseif ($this->from == self::$aMonthAgo) return 'A month ago';
        elseif ($this->from != null) return $this->from;
        else return null;*/
    }

    /**
     * @param mixed $from
     */
    public function setFrom($from)
    {
        //$this->from = $from;
        if ($from == 'Yesterday') $this->from = self::$yesterday;
        elseif ($from == 'A week ago') $this->from = self::$aWeekAgo;
        elseif ($from == 'A month ago') $this->from = self::$aMonthAgo;
        elseif ($from != null) $this->from = $from; // need to check the value!!! all of the values must be checked
        else $this->from = null;
    }

    /**
     * @return mixed
     */
    public function getTo()
    {
        return $this->to;
        /*if ($this->to == self::$yesterday) return 'Yesterday';
        elseif ($this->to == self::$aWeekAgo) return 'A week ago';
        elseif ($this->to == self::$aMonthAgo) return 'A month ago';
        elseif ($this->to != null) return $this->to;
        else return null;*/
    }

    /**
     * @param mixed $to
     */
    public function setTo($to)
    {
        //$this->to = $to;
        if ($to == 'Yesterday') $this->to = self::$yesterday;
        elseif ($to == 'A week ago') $this->to = self::$aWeekAgo;
        elseif ($to == 'A month ago') $this->to = self::$aMonthAgo;
        elseif ($to != null) $this->to = $to; // need to check the value!!! all of the values must be checked
        else $this->to = null;
    }

    /**
     * @return string
     */
    public function getColumn(): string
    {
        return $this->column;
    }

    /**
     * @param mixed $column
     * @throws \Exception
     */
    public function setColumn($column)
    {
        if( !self::$columns_available->contains($column) )
            throw new \Exception("Bad module configuration: " . $column . " is not available as a column.");

        $this->column = $column;
    }

    /**
     * @return string
     */
    public function getOperator(): string
    {
        return $this->operator;
    }

    /**
     * @param string $operator
     * @throws \Exception
     */
    public function setOperator(string $operator = null)
    {
        if( !self::$operators_available->contains($operator) && $operator != null)
            throw new \Exception("Bad module configuration: " . $operator . " is not available as a an operator.");

        $this->operator = $operator;
    }

    /**
     * @return bool
     */
    public function isNegate(): bool
    {
        return $this->negate;
    }

    /**
     * @param bool $negate
     */
    public function setNegate(bool $negate)
    {
        $this->negate = $negate;
    }


}