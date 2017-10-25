<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 13/10/17
 * Time: 6:13 PM
 */

namespace AppBundle\Library;


use Doctrine\ORM\Query\Expr\Andx;
use Doctrine\ORM\Query\Expr\Orx;
use Doctrine\ORM\QueryBuilder;

class ConditionTree
{
    /** @var Node */
    private $root;

    public function  __construct() {
        $this->root = null;
    }

    /**
     * Adds a Node to the left most available position
     * @param $content
     * @throws \Exception : if the tree is full
     */
    public function add($content) { //&$content
        $node = new Node($content);

        if($this->root == null) $this->root = $node;
        else {
            $current = $this->root;

            $depth = 1;

            if( $current->isFull() ) throw new \Exception("Trying to add Node to ConditionTree while it's full!");

            while(true) {
                if( $current->getLeft() == null ) {$current->setLeft($node)->setDepth($depth); break;}
                else if( !$current->getLeft()->isFull() ) $current = $current->getLeft();
                else if( $current->getRight() == null ) {$current->setRight($node)->setDepth($depth); break;}
                else if( !$current->getRight()->isFull() ) $current = $current->getRight();
                $depth++;
            }
        }
    }

    public function getFullExpr(QueryBuilder $builder, string $paramName){
        $current = $this->getDeepestNode();

        $key = 0;
        $parameters = [];

        if($current === $this->root) {
            $part = $this->makePartialExpression($builder, $current, $paramName, ++$key);
            if($part['parameter'] != null) $parameters[$paramName . $key] = $part['parameter'];
            $this->replace($current, new Node($part['expr']));
        }
        else {
            while ($current !== $this->root){
                /** @var Node $sibling */
                $sibling = $current->getSibling();
                if( is_string($sibling->getContent()) ) $current = $sibling->getDeepestNode();
                else {
                    $part1 = $this->makePartialExpression($builder, $current, $paramName, ++$key);
                    if($part1['parameter'] != null) $parameters[$paramName . $key] = $part1['parameter'];

                    $part2 = $this->makePartialExpression($builder, $sibling, $paramName, ++$key);
                    if($part2['parameter'] != null) $parameters[$paramName . $key] = $part2['parameter'];

                    if($current->getParent()->getContent() == '&') $Q = new Node($builder->expr()->andX($part1['expr'], $part2['expr']));
                    else $Q = new Node($builder->expr()->orX($part1['expr'], $part2['expr']));

                    $this->replace($current->getParent(), $Q);
                    $current = $Q;
                }
            }
        }

        return ['fullExpr' => $this->root->getContent(), 'parameters' => $parameters];
    }

    /**
     * List of all valid operators:
     *      - '='       : eq
     *      - '!='      : neq
     *      - '<='      : lte
     *      - '>='      : gte
     *      - '<'       : lt
     *      - '>'       : gt
     *      - '~'       : like
     *      - '!~'       : notLike
     *
     * @param QueryBuilder $builder
     * @param Node $node
     * @param string $paramName
     * @param int $key
     * @return array
     * @throws \Exception
     */
    private function makePartialExpression(QueryBuilder $builder, Node $node, string $paramName, int $key){
        /**
         * TODO: add operator to them
         */
        $parameter = null;
        if( !$node->getContent() instanceof Andx && !$node->getContent() instanceof Orx ){
            $fullColumnName = $node->getContent()->getField()->getFullColumnName();
            $param = ":$paramName" . $key;

            switch ($node->getContent()->getOperator()) {
                case '=':   $part = $builder->expr()->eq($fullColumnName, $param);      break;
                case '!=':  $part = $builder->expr()->neq($fullColumnName, $param);     break;
                case '<=':  $part = $builder->expr()->lte($fullColumnName, $param);     break;
                case '>=':  $part = $builder->expr()->gte($fullColumnName, $param);     break;
                case '<':   $part = $builder->expr()->lt($fullColumnName, $param);      break;
                case '>':   $part = $builder->expr()->gt($fullColumnName, $param);      break;
                case '~':   $part = $builder->expr()->like($fullColumnName, $param);    break;
                case '!~':  $part = $builder->expr()->notLike($fullColumnName, $param); break;
                default: throw new \Exception("Operator '" . $node->getContent()->getOperator() . "' is not a valid operator!");
            }

            $parameter = $node->getContent()->getValue();
        } else $part = $node->getContent();

        return ['expr' => $part, 'parameter' => $parameter];
    }

    public function replace(Node $node, Node $other){
        $p = $node->getParent();
        if($p == null) $this->root = $other->setDepth(0);
        else {
            $other->setDepth($node->getDepth())->setParent($p);
            if($p->getLeft() === $node) $p->setLeft($other);
            else $p->setRight($other);
        }
        unset($node);
    }

    //from left most side
    public function getDeepestNode(){
        return $this->root->getDeepestNode();
    }

/*
    public function add($content) { //&$content
        $node = new Node($content);

        if($this->root == null) $this->root = $node;
        else {
            /** @var Node|null /
            $parent = null;
            $current = $this->root;

            while(true) {

                $currContent = $current->getContent();

                if( is_string($currContent) ){
                    if($current->getLeft() == null) {
                        $current->setLeft($node);
                        break;
                    }
                    else if($current->getRight() == null) {
                        $current->setRight($node);
                        break;
                    }
                }

                if( is_string($current->getLeft()->getContent()) ) {
                    $parent = $current;
                    $current = $current->getLeft();
                }
                else if( is_string($current->getRight()->getContent()) ) {
                    $parent = $current;
                    $current = $current->getRight();
                }
                else if( $current !== $parent->getRight() ) $current = $parent->getRight();
                else throw new \Exception("'ConditionTree' is full!");

            }
        }
    }
*/
}