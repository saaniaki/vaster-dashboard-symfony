<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 12/10/17
 * Time: 3:52 PM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AppFieldInfoRepository")
 * @ORM\Table(name="vaster_data_sources")
 */
class FieldInfo
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="bigint", nullable=false)
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $sourceAlias;

    /**
     * @ORM\Column(type="string")
     */
    private $fieldAlias;

    /**
     * Determines if the source type is SnapShot Data or not
     * @ORM\Column(type="boolean")
     */
    private $snapShot = false;

    /**
     * Entity to start with
     * @ORM\Column(type="string")
     */
    private $entity;

    /**
     * @ORM\Column(type="string")
     */
    private $table;

    /**
     * @ORM\Column(type="string")
     */
    private $column;

    /**
     * Determines the type of the field {'Simple', 'Date', 'SnapShot'}
     * which helps data source object to find out the method of grabbing the data
     * @ORM\Column(type="string")
     */
    private $dateType = false;

    //available values nullable

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getSourceAlias()
    {
        return $this->sourceAlias;
    }

    /**
     * @return mixed
     */
    public function getFieldAlias()
    {
        return $this->fieldAlias;
    }

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @return mixed
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @return mixed
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     * @return bool
     */
    public function isSnapshot() :bool
    {
        return $this->snapShot;
    }

    /**
     * @return bool
     */
    public function isDateType() :bool
    {
        return $this->dateType;
    }


}