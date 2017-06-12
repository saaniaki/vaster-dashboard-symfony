<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 12/06/17
 * Time: 3:19 PM
 */

namespace AppBundle\Module\Graph;


use AppBundle\Entity\Module;
use AppBundle\Module\ModuleInterface;
use Doctrine\Common\Persistence\ManagerRegistry;

class module1 implements ModuleInterface
{
    /**
     * @var Module
     */
    private $module;
    private $managerRegistry;

    private $title;
    private $type;
    private $xTitle;
    private $yTitle;
    private $xValues;
    private $yValues;
    private $interval;
    private $data1 = [];
    private $data1_name;

    public function __construct(Module $module, ManagerRegistry $managerRegistry)
    {
        $this->module = $module;
        $this->managerRegistry = $managerRegistry;
        $this->title = 'Registration Over Time';
        $this->type = 'line';
        //$this->xTitle = 'Time';
        $this->yTitle = 'Registration';
        $this->interval = 24 * 3600 * 1000;
    }

    public function render()
    {
        $em = $this->managerRegistry->getManager('vaster');
        $data = $em->getRepository('VasterBundle:User')->findAll();


        $this->data1 = [
            [
                'x' => 1,
                'y' => 5,
                'name' => 'point1'
            ],
            [
                'x' => 2,
                'y' => 3,
                'name' => 'point2'
            ]
        ];



        $this->data1_name = 'registration';
        return get_object_vars($this);
    }
}