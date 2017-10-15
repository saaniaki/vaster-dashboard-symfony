<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 06/10/17
 * Time: 5:40 PM
 */

namespace AppBundle\Module\Configuration;

use AppBundle\Entity\FieldInfo;
use AppBundle\Service\vdpModule;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class DataSource
{
    /** @var FieldInfo : an entity object which hold the information about this field */
    private $field;
    /** @var bool : if true, the date categories are necessary since specific snap shots should be selected */
    private $specific;
    /** @var EntityRepository : the repository which grabbing data should start from */
    private $repository;

    public function __construct(string $sourceAlias, string $fieldAlias, bool $snapShot = false, bool $specific = false)
    {
        $this->specific = $specific;
        $this->field = vdpModule::$fieldInfoService->validate($sourceAlias, $fieldAlias, $snapShot);
        $this->repository = vdpModule::$fieldInfoService->getRepository($this->field->getEntity());
    }

    /**
     * This function can be combined with initQuery()
     * @return EntityRepository
     */
    public function getRepository(){
        return $this->repository;
    }

    /**
     * Initiates the query building process and returns the basic
     * SQL query to grab the data from database
     * @return QueryBuilder
     */
    public function initQuery(){
        $entityAlias = strtolower(explode(':', $this->field->getEntity())[1]);
        $fullColumnName = "{$this->field->getTable()}.{$this->field->getColumn()}";
        $title = str_replace(' ', '_', $this->field->getFieldAlias());

        // Initialise the query and selects the column
        $query = $this->getRepository()
            ->createQueryBuilder($entityAlias)
            ->select("$fullColumnName $title, user.userid"); // user.userid is added only for testing purposes

        // JOIN a related table if it is necessary
        if($this->field->getTable() != $entityAlias)
            $query->leftJoin("$entityAlias.{$this->field->getTable()}", $this->field->getTable());

        // JOIN to users table if data source is from snapshot table and sort by the shot date
        if($this->field->isSnapshot()) {
            $query->leftJoin("$entityAlias.user", 'user');
            $query->orderBy("$entityAlias.timestamp", 'DESC');
        }

        // JOIN to users table if data source is from snapshot table and sort by the shot date
        if($this->field->isDateType())
            $query->orderBy($fullColumnName, 'DESC');

        return $query;
    }

    public function applyCondition(QueryBuilder $query, Condition $condition){

    }
}