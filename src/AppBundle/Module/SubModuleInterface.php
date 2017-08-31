<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 2017-08-20
 * Time: 9:55 AM
 */

namespace AppBundle\Module;

use AppBundle\Entity\Module;
use AppBundle\Module\Configuration\Configuration;
use AppBundle\Module\Configuration\Filters;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;

interface SubModuleInterface
{
    public function __construct(ModuleInterface $vdpModule, ObjectManager $em);

    public function count(Combination $combo = null, Filters $filters = null);
    public function getColumn(Combination $combo = null, Filters $filters = null);
    public function getStartingNumber($from, Combination $combo = null, Filters $filters = null);
    public function getFooter(int $currentlyShowing);
    public function getGeneralName();
    public function getUnit();
}