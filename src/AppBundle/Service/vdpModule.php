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
    /** @var ManagerRegistry */
    private $managerRegistry;
    /** @var FieldInfoService */
    public static $fieldInfoService;

    public function __construct(ManagerRegistry $managerRegistry, FieldInfoService $dataSourceValidator)
    {
        $this->managerRegistry = $managerRegistry;
        $this::$fieldInfoService = $dataSourceValidator;
    }

    public function render(Module $module){
        $type = $module->getModuleInfo()->getType();
        /** @var $instance ModuleInterface*/
        $instance = new $type($module, $this->managerRegistry);
        return $instance->render();
    }
}