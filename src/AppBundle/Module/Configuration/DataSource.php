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
use Doctrine\ORM\Query\Parameter;
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
     * @return QueryBuilder
     */
    public function getQueryBuilder(){
        return $this->repository->createQueryBuilder($this->field->getEntityAlias());
    }

    /**
     * Initiates the query building process and returns the basic
     * SQL query to grab the data from database
     * @return QueryBuilder
     */
    public function initQuery(){
        $entityAlias = $this->field->getEntityAlias();
        $fullColumnName = $this->field->getFullColumnName();

        // Initialise the query and selects the column
        $query = $this->getQueryBuilder()
            ->select("$fullColumnName {$this->field->getMachineTitle()}"); // user.userid can be added for testing purposes (, user.userid)

        // JOIN a related table if it is necessary
        if($this->field->getTable() != $entityAlias)
            $query->leftJoin("$entityAlias.{$this->field->getTable()}", $this->field->getTable());

        // JOIN to users table if data source is from snapshot table and sort by the shot date
        if($this->field->isSnapshot()) {
            $query->addSelect("$entityAlias.timestamp dateGroup");      //dateGroup is defined here, It's a keyword
            $query->leftJoin("$entityAlias.user", 'user');
            $query->orderBy("$entityAlias.timestamp", 'ASC');
        } else $query->groupBy('user.userid'); //important when doing filter on current data but grabbing from snapshots

        // JOIN to users table if data source is from snapshot table and sort by the shot date
        if($this->field->isDateType())
            $query->orderBy($fullColumnName, 'ASC');


        //dump($query->getQuery());die();
        return $query;
    }

    /**
     * Applies a Condition on the Query.
     * First ensures that all needed tables are joined
     * Next, modifies the Query by using a ConditionTree
     * NOTE: to bind both expression and parameters, getFullExpr function
     * in ConditionTree returns an array with indexes 'fullExpr' and 'parameters'
     * @param QueryBuilder $query
     * @param Condition $condition
     * @return QueryBuilder
     */
    public function applyCondition(QueryBuilder $query, Condition $condition){
        // JOIN a related table if it is necessary
        $aliases = $query->getAllAliases();
        if($this->field->isSnapshot()) $entityAlias = 'user';
        else $entityAlias = $this->field->getEntityAlias();
        foreach ($condition->getDependencies() as $dependency)
            if (!in_array($dependency, $aliases))
                $query->leftJoin("$entityAlias.$dependency", $dependency);


        //Analyse relation and modify the query as needed
        $tree = $condition->makeConditionTree();
        $fullExpr = $tree->getFullExpr($this->getQueryBuilder(), $condition->getTitle());
        $query->andWhere($fullExpr['fullExpr']);

        //Bind all parameters including the old ones
        $oldParameters = $query->getParameters();
        while ($oldParameters->current() != false){
            /** @var Parameter $parameter */
            $parameter = $oldParameters->current();
            $fullExpr['parameters'][$parameter->getName()] = $parameter->getValue();
            $oldParameters->next();
        }
        $query->setParameters($fullExpr['parameters']);

        return $query;
    }

    /**
     * @return FieldInfo
     */
    public function getField(): FieldInfo
    {
        return $this->field;
    }


}