<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 13/10/17
 * Time: 6:08 PM
 */

namespace AppBundle\Library;


use AppBundle\Module\Configuration\Expression;
use Doctrine\ORM\Query\Expr\Andx;
use Doctrine\ORM\Query\Expr\Orx;

class Node
{
    private $content; // string or expression
    private $left;
    private $right;
    private $parent;
    private $depth;

    public function __construct($content, $depth = 0) {
        $this->content = $content;
        $this->left = null;
        $this->right = null;
        $this->parent = null;
        $this->depth = 0;
    }

    /**
     * Returns the sibling of a Node
     * @return Node
     */
    public function getSibling(){
        $p = $this->getParent();
        if($p->getLeft() === $this) return $p->getRight();
        else return $p->getLeft();
    }

    /**
     * Returns true if this Node is full and false vice versa.
     * @return bool
     */
    public function isFull(){
        if( !is_string($this->getContent()) ) return true;
        else if($this->getLeft() == null || $this->getRight() == null) return false;

        return $this->getLeft()->isFull() && $this->getRight()->isFull();
    }

    /**
     * Returns the deepest node on this branch
     * @return Node
     */
    public function getDeepestNode(){
        $left = $this->getLeft();
        $right = $this->getRight();

        if($left == null && $right == null) return $this;
        else if ($left == null) return $right;
        else if ($right == null) return $left;
        else {
            $leftResult = null;
            $rightResult = null;

            if( !is_string($left->getContent()) ) $leftResult = $left;
            else $leftResult = $left->getDeepestNode();

            if( !is_string($right->getContent()) ) $rightResult = $right;
            else $rightResult = $right->getDeepestNode();

            if( $leftResult->getDepth() < $rightResult->getDepth() ) return $rightResult;
            else return $leftResult;
        }
    }

    /**
     * @return string|Expression|Andx|Orx
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string|Expression $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return Node|null
     */
    public function getLeft() :?Node
    {
        return $this->left;
    }

    /**
     * @param Node $left
     * @return $this
     */
    public function setLeft(Node $left)
    {
        $left->setParent($this);
        $this->left = $left;
        return $this->left;
    }

    /**
     * @return Node|null
     */
    public function getRight() :?Node
    {
        return $this->right;
    }

    /**
     * @param Node $right
     * @return $this
     */
    public function setRight(Node $right)
    {
        $right->setParent($this);
        $this->right = $right;
        return $this->right;
    }

    /**
     * @return null
     */
    public function getDepth()
    {
        return $this->depth;
    }

    /**
     * @param null $depth
     * @return $this
     */
    public function setDepth($depth)
    {
        $this->depth = $depth;
        return $this;
    }

    /**
     * @return Node|null
     */
    public function getParent() :?Node
    {
        return $this->parent;
    }

    /**
     * @param Node $parent
     * @return $this
     */
    public function setParent(Node $parent)
    {
        $this->parent = $parent;
        return $this->parent;
    }

}