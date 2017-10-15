<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 13/10/17
 * Time: 6:13 PM
 */

namespace AppBundle\Library;


class ConditionTree
{
    /** @var Node */
    private $root;

    public function  __construct() {
        $this->root = null;
    }

    public function add($content) { //&$content
        $node = new Node($content);

        if($this->root == null) $this->root = $node;
        else {
            /** @var Node|null */
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

}