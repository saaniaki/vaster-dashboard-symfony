<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 10/06/17
 * Time: 12:53 PM
 */

namespace AppBundle\Module;


use AppBundle\Entity\Module;
use AppBundle\Module\Configuration\Configuration;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ManagerRegistry;

interface ModuleInterface
{
    public function __construct(Module $module, ManagerRegistry $managerRegistry);
    public function render();
}