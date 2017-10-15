<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 13/10/17
 * Time: 5:01 PM
 */

namespace AppBundle\Module\Configuration;


use Doctrine\Common\Collections\ArrayCollection;

class Settings
{
    /** @var DataSource */
    private $data;
    /** @var Condition */
    private $filters;

    public function __construct(ArrayCollection $data = null)
    {
        if( $data != null ) $this->load($data);
    }

    /**
     * @param DataSource $data
     */
    public function setDataSource(DataSource $data)
    {
        $this->data = $data;
    }

    /**
     * @return DataSource
     */
    public function getDataSource(): DataSource
    {
        return $this->data;
    }

    /**
     * @param Condition $filters
     */
    public function setFilters(Condition $filters)
    {
        $this->filters = $filters;
    }

    /**
     * @return Condition
     */
    public function getFilters(): Condition
    {
        return $this->filters;
    }




    public function load(ArrayCollection $JSON)
    {
        $data = $JSON->get('data');
        //$categories = $JSON_data->get('categories');
        $filters = $JSON->get('filters');
        //$layout = $JSON_data->get('layout');
        //$presentation = $JSON_data->get('presentation');

        ////////////////////////////////////////////////////////////////////////// Data: creating $dataObj
        $data['field'] = explode(": ",$data['field']);
        $dataObj = new DataSource($data['field'][0], $data['field'][1], $data['snapShot'], $data['specific']);

        ////////////////////////////////////////////////////////////////////////// Filters: creating $filtersObj

        $filtersObj = new Condition('filters');
        foreach ( $filters as $indicator => $filter ){
            if(isset($filter['field'])) {
                $filter['field'] = explode(": ", $filter['field']);
                $filtersObj->addExpressions(new Expression($indicator, $filter['field'][0], $filter['field'][1], $filter['operator'], $filter['value']));
            }else $filtersObj->setRelation($filter); // this is the "relation" string
        }


        ////////////////////////////////////////////////////////////////////////// Configuration: setting up $configuration
        if( isset($dataObj) && $dataObj != null )                   $this->setDataSource($dataObj);
        if( isset($filtersObj) && $filtersObj != null )             $this->setFilters($filtersObj);

    }
}