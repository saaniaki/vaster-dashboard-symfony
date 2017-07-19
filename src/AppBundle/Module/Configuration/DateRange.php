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
    public static $columns_available = ['user.createdtime'];

    public $from;
    public $to;
    public $column;
    public $operator;
    public $negate = false;

    public function __construct()
    {
        if( !isset(self::$operators_available) )
            self::$operators_available = new ArrayCollection(['and', 'or']);

        if( is_array(self::$columns_available) )
            self::$columns_available = new ArrayCollection(self::$columns_available);

        $this->setColumn('user.createdtime');
        $this->setOperator('or');
    }

    /**
     * @return mixed
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param mixed $from
     */
    public function setFrom($from)
    {
        $this->from = $from;
    }

    /**
     * @return mixed
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param mixed $to
     */
    public function setTo($to)
    {
        $this->to = $to;
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