<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 2017-06-16
 * Time: 1:11 PM
 */

namespace AppBundle\Repository;

use AppBundle\Entity\FieldInfo;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class AppFieldInfoRepository extends EntityRepository
{
    /**
     * Assures the source alias and field alias match a record in the database
     * and returns the matched VasterDataSources object
     * @param String $sourceAlias
     * @param String $fieldAlias
     * @param bool $snapShot
     * @return FieldInfo
     */
    function validate(String $sourceAlias, String $fieldAlias, Bool $snapShot){
        return $this->createQueryBuilder('dataSource')->select('dataSource')
            ->where('dataSource.sourceAlias = :sourceAlias')
            ->andWhere('dataSource.fieldAlias = :fieldAlias')
            ->andWhere('dataSource.snapShot = :snapShot')
            ->setParameters(['sourceAlias' => $sourceAlias, 'fieldAlias' => $fieldAlias, 'snapShot' => $snapShot])
            ->getQuery()
            ->getSingleResult();
    }
}