<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 10/06/17
 * Time: 12:39 PM
 */

namespace AppBundle\Service;


use AppBundle\Entity\Module;
use AppBundle\Module\ModuleInterface;
use Doctrine\Common\Persistence\ManagerRegistry;

class vdpModule
{

    private $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {

        $this->managerRegistry = $managerRegistry;
    }

    public function render(Module $module){

        //$em = $this->managerRegistry->getManager();
        //$em->getRepository('AppBundle:Module');

        $info = $module->getModuleInfo();
        $type = $info->getType();
        /** @var $instance ModuleInterface*/
        $instance = new $type($module, $this->managerRegistry);
        return $instance->render();
    }
}