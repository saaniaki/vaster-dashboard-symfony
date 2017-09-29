<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 14/07/17
 * Time: 11:17 AM
 */

namespace AppBundle\Module\Configuration;

use Doctrine\Common\Collections\ArrayCollection;

class Search
{
    /** @var $operators_available ArrayCollection */
    private static $operators_available;

    /** @var $columns_available ArrayCollection */
    public static $columns_available = [
        'User: First Name' => 'user.firstname',
        'User: Last Name' => 'user.lastname',
        'User: Email' => 'user.email',
        'User: Phone' => 'user.phone',
        'Search: Query' => 'searches.searchquery'
    ];


    public $keyword;
    public $columns = [];
    public $columnOperator;
    public $expressionOperator;
    public $negate = false;

    public function __construct()
    {
        if( !isset(self::$operators_available) )
            self::$operators_available = new ArrayCollection(['and', 'or']);

        if( is_array(self::$columns_available) )
            self::$columns_available = new ArrayCollection(self::$columns_available);

        //getAvailableColumns()

        $this->setColumnOperator('or');
        $this->setExpressionOperator('or');
    }

    /**
     * @return string
     */
    public function getKeyword(): string
    {
        return $this->keyword;
    }

    /**
     * @param string $keyword
     * @throws \Exception
     */
    public function setKeyword(string $keyword = null)
    {
        $this->keyword = $keyword;
    }

    /**
     * @return string[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @param array $columns
     * @throws \Exception
     */
    public function setColumns(array $columns)
    {
        foreach ($columns as $col){
            //if( self::$columns_available->contains($col) ) $col = array_search($col, self::$columns_available->toArray()); //low performance
            $this->addColumn($col);
        }

    }

    /**
     * @param string $column
     * @throws \Exception
     */
    public function addColumn(string $column)
    {
        if( !self::$columns_available->containsKey($column) )
            throw new \Exception("Bad module configuration: " . $column . " is not available as a column.");

        $this->columns[] = $column;
    }

    /**
     * @return string
     */
    public function getColumnOperator(): string
    {
        return $this->columnOperator;
    }

    /**
     * @param string $operator
     * @throws \Exception
     */
    public function setColumnOperator(string $operator)
    {
        if( !self::$operators_available->contains($operator) && $operator != null)
            throw new \Exception("Bad module configuration: " . $operator . " is not available as a an operator.");

        $this->columnOperator = $operator;
    }

    /**
     * @return string
     */
    public function getExpressionOperator(): string
    {
        return $this->expressionOperator;
    }

    /**
     * @param string $operator
     * @throws \Exception
     */
    public function setExpressionOperator(string $operator = null)
    {
        if( !self::$operators_available->contains($operator) && $operator != null)
            throw new \Exception("Bad module configuration: " . $operator . " is not available as a an operator.");

        $this->expressionOperator = $operator;
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

    public static function getAvailableColumns(){
        if( is_array(self::$columns_available) )
            self::$columns_available = new ArrayCollection(self::$columns_available);

        return self::$columns_available->getKeys();
    }

}