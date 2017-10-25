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
    /** @var Expression[] : an array of expressions */
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
     * @return Expression[]
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
        //validate $this->getRelation()
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
        return $this->stackToTree();
    }

    private function stackToTree(Stack $stack = null, ConditionTree $result = null){
        if($stack == null) $stack = $this->relationToStack();
        if($result == null) $result = new ConditionTree();

        $string = $stack->pop();

        if( strlen($string) == 1 ) $result->add($this->findExpression($string));
        else {
            $result->add($string{1});

            $numSub = substr_count($string, '$');

            if( $numSub == 2 ){
                $this->stackToTree($stack, $result);
                $this->stackToTree($stack, $result);
            }else {
                if( $string{0} != '$') $result->add($this->findExpression($string{0}));
                if( $string{2} != '$') $result->add($this->findExpression($string{2}));
                if( $numSub == 1 ) $this->stackToTree($stack, $result);
            }
        }

        return $result;
    }

    private function relationToStack(string $string = null, Stack $stack = null){
        if($string == null) $string = $this->getRelation();
        if($stack == null) $stack = new Stack();

        $start = null;
        $end = null;

        $stringArray = str_split($string);
        foreach($stringArray as $i => $char){
            if ( $char == "(" ) $start = $i;
            else if ( $char == ")" ) {$end = $i; break;};
        }

        $str1 = substr($string,0,$start);
        $str2 = substr($string,$end+1);

        $output = substr($string, $start+1, $end-$start-1);
        $stack->push($output);
        $next = $str1."$".$str2;
        //dump($next);

        if( $next == '$' ) return $stack;
        else $this->relationToStack($str1."$".$str2, $stack);
        return $stack;
    }

    private function findExpression(string $indicator){
        foreach ($this->getExpressions() as $expr) if($expr->getIndicator() == $indicator) return $expr;
        return null;
    }

    /**
     * Get all of aliases of tables which this condition should apply on
     * @return array
     */
    public function getDependencies(){
        $aliases = [];
        foreach ($this->getExpressions() as $expr)
            if( !in_array($expr->getField()->getTable(), $aliases) )
                $aliases[] = $expr->getField()->getTable();
        return $aliases;
    }
}