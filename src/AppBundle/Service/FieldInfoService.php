<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 06/10/17
 * Time: 4:45 PM
 */

namespace AppBundle\Service;


use AppBundle\Entity\FieldInfo;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityRepository;

class FieldInfoService
{
    private $repository;
    private $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
        $this->repository = $this->managerRegistry->getManager()->getRepository('AppBundle:FieldInfo');
    }

    /**
     * Connects the AppVasterDataSourceRepository to the service
     * @param String $sourceAlias
     * @param String $fieldAlias
     * @param bool $snapShot
     * @return FieldInfo
     */
    public function validate(string $sourceAlias, string $fieldAlias, bool $snapShot){
        return $this->repository->validate($sourceAlias, $fieldAlias, $snapShot);
    }

    /**
     * This function can be combined with initQuery()
     * @param String $entityName
     * @return EntityRepository
     */
    public function getRepository(String $entityName){
        /** @var EntityRepository $rep */
        $rep = $this->managerRegistry->getManager('vaster')->getRepository($entityName);
        return $rep;
    }
}