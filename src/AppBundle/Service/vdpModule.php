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
use Doctrine\Common\Collections\ArrayCollection;
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



        if($module->getAnalytics() == null) {
            $module->setAnalytics('default');
        }

        if($module->getUserType() == null) {
            $module->setUserType('all');
        }

        $configuration = new ArrayCollection([
            'analytics' => $module->getAnalytics(),
            'userType' => $module->getUserType(),
            'keyword' => $module->getKeyword(),
            'fromDate' => $module->getFromDate(),
            'toDate' => $module->getToDate(),
        ]);


        return $instance->render($configuration);
    }
}