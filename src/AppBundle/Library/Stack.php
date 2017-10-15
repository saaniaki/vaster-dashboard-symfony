<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 13/10/17
 * Time: 8:33 PM
 */

namespace AppBundle\Library;


class Stack
{
    private $array = [];

    function __construct(array $array = null)
    {
        foreach ($array as $element) $this->push($element);
    }

    public function push($element){
        return array_push($this->array, $element);
    }

    public function pop(){
        return array_pop($this->array);
    }

    public function top(){
        return end($this->array);
    }

    public function isEmpty(){
        return empty($this->array);
    }
}