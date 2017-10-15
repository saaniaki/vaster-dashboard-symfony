<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 13/10/17
 * Time: 3:06 PM
 */

namespace AppBundle\Module\Configuration;


use AppBundle\Library\ConditionTree;
use AppBundle\Library\Stack;

class Condition
{
    /** @var string : a title */
    private $title;
    /** @var array : an array of expressions */
    private $expressions = [];
    /** @var string */
    private $relation;

    public function __construct(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param Expression $expression
     */
    public function addExpressions(Expression $expression)
    {
        $this->expressions[] = $expression;
    }

    /**
     * @return array
     */
    public function getExpressions(): array
    {
        return $this->expressions;
    }

    /**
     * @param string $relation
     */
    public function setRelation(string $relation)
    {
        $this->relation = $relation;
    }

    /**
     * @return string
     */
    public function getRelation(): string
    {
        return $this->relation;
    }

    public function makeConditionTree(){
        $stack = new Stack();






        $tree = new ConditionTree();




        return $tree;
    }

}