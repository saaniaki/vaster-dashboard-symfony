<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 10/06/17
 * Time: 12:53 PM
 */

namespace AppBundle\Module;


use AppBundle\Entity\Module;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ManagerRegistry;

interface ModuleInterface
{
    function __construct(Module $module, ManagerRegistry $managerRegistry);

    public function render(ArrayCollection $configuration);
}