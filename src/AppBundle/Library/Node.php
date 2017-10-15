<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 13/10/17
 * Time: 6:08 PM
 */

namespace AppBundle\Library;


use AppBundle\Module\Configuration\Expression;

class Node
{
    private $content; // string or expression
    private $left;
    private $right;
    //public $level;

    public function __construct($content) {
        $this->content = $content;
        $this->left = null;
        $this->right = null;
        //$this->level = NULL;
    }

    /**
     * @return string|Expression
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
     * @param null $left
     */
    public function setLeft($left)
    {
        $this->left = $left;
    }

    /**
     * @return Node|null
     */
    public function getRight() :?Node
    {
        return $this->right;
    }

    /**
     * @param null $right
     */
    public function setRight($right)
    {
        $this->right = $right;
    }

}