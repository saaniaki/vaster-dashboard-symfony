<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 22/07/17
 * Time: 3:39 PM
 */

namespace AppBundle\Module\Configuration;


class Layout
{

    /** @var $title string */
    public $title;
    /** @var $size integer */
    public $size;

    public function __construct($title = null, $size = 3)
    {
        if($size > 0 && $size <= 12)
            $this->size = $size;
        $this->title = $title;
    }

    /**
     * @return string|null
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @param int $size
     */
    public function setSize(int $size)
    {
        $this->size = $size;
    }



}