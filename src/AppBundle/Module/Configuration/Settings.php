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
    private $data; //change name to dataSource
    /** @var ArrayCollection|Condition[] */
    private $categories;
    /** @var Condition */
    private $filters;

    public $presentation;
    public $layout;

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
     * @return Condition[]|ArrayCollection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param Condition[]|ArrayCollection $categories
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
    }

    /**
     * @param Condition $filters
     */
    public function setFilters(Condition $filters)
    {
        $this->filters = $filters;
    }

    /**
     * @return Condition|null
     */
    public function getFilters(): ?Condition
    {
        return $this->filters;
    }




    public function load(ArrayCollection $JSON)
    {
        $data = $JSON->get('data');
        $categories = $JSON->get('categories');
        $filters = $JSON->get('filters');
        $layout = $JSON->get('layout');
        $presentation = $JSON->get('presentation');

        ////////////////////////////////////////////////////////////////////////// Data: creating $dataObj
        $data['field'] = explode(": ",$data['field']);
        $dataObj = new DataSource($data['field'][0], $data['field'][1], $data['snapShot'], $data['specific']);

        ////////////////////////////////////////////////////////////////////////// Filters: creating $filtersObj

        if($filters != null){
            $filtersObj = new Condition('filters');
            foreach ( $filters as $indicator => $expr ){
                if(isset($expr['field'])) {
                    $expr['field'] = explode(": ", $expr['field']);
                    $filtersObj->addExpressions(new Expression($indicator, $expr['field'][0], $expr['field'][1], $expr['snapShot'], $expr['operator'], $expr['value']));
                }else $filtersObj->setRelation($expr); // this is the "relation" string
            }
        }

        ////////////////////////////////////////////////////////////////////////// Categories: creating $categoriesObj

        $categoriesObj = new ArrayCollection();
        foreach ( $categories as $name => $category ){
            $condition = new Condition($name);
            foreach ( $category as $indicator => $expr ){
                if(isset($expr['field'])) {
                    $expr['field'] = explode(": ", $expr['field']);
                    $condition->addExpressions(new Expression($indicator, $expr['field'][0], $expr['field'][1], $expr['snapShot'], $expr['operator'], $expr['value']));
                }else $condition->setRelation($expr); // this is the "relation" string
            }
            $categoriesObj->add($condition);
        }

        ////////////////////////////////////////////////////////////////////////// Layout: creating $layoutObj

        if( $layout != null ){
            $layoutObj = new Layout();
            if( isset($layout['title']) && $layout['title'] != null )$layoutObj->setTitle($layout['title']);
            if( isset($layout['size']) && $layout['size'] != null )$layoutObj->setSize($layout['size']);
        }

        ////////////////////////////////////////////////////////////////////////// Presentation: creating $presentationObj

        if( $presentation != null){
            $presentationObj = new Presentation();
            if( isset($presentation['data']) && $presentation['data'] != null )$presentationObj->setData($presentation['data']);
            if( isset($presentation['interval']) && $presentation['interval'] != null )$presentationObj->setInterval($presentation['interval']);
            if( isset($presentation['snapShots']) && $presentation['snapShots'] != null )$presentationObj->setSnapShots($presentation['snapShots']);
            if( isset($presentation['zero'])){$presentationObj->setZero($presentation['zero']);}
        }

        ////////////////////////////////////////////////////////////////////////// Configuration: setting up $configuration
        if( isset($dataObj) && $dataObj != null )                   $this->setDataSource($dataObj);
        if( isset($categoriesObj) && $categoriesObj != null )       $this->setCategories($categoriesObj);
        if( isset($filtersObj) && $filtersObj != null )             $this->setFilters($filtersObj);

        if( isset($layoutObj) && $layoutObj != null )               $this->setLayout($layoutObj);
        if( isset($presentationObj) && $presentationObj != null )   $this->setPresentation($presentationObj);

    }




    /**
     * @return Presentation
     */
    public function getPresentation(): ?Presentation
    {
        return $this->presentation;
    }

    /**
     * @param Presentation $presentation
     */
    public function setPresentation(Presentation $presentation = null)
    {
        $this->presentation = $presentation;
    }


    /**
     * @return Layout
     */
    public function getLayout(): Layout
    {
        return $this->layout;
    }

    /**
     * @param Layout $layout
     */
    public function setLayout(Layout $layout)
    {
        $this->layout = $layout;
    }
}