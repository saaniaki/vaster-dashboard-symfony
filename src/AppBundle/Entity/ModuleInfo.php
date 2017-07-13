<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 2017-06-09
 * Time: 12:30 PM
 */

namespace AppBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="moduleInfo")
 */
class ModuleInfo
{
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint", nullable=false)
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     */
    private $type;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="text")
     */
    private $guide;

    /**
     * @ORM\Column(type="json_array")
     */
    private $availableAnalytics;

    /**
     * @ORM\Column(type="json_array")
     */
    private $availableConfiguration;

    /**
     * @return mixed
     */
    public function getAvailableConfiguration()
    {
        return $this->availableConfiguration;
    }

    /**
     * @param mixed $availableConfiguration
     */
    public function setAvailableConfiguration($availableConfiguration)
    {
        $this->availableConfiguration = $availableConfiguration;
    }

    /**
     * @return mixed
     */
    public function getAvailableAnalytics()
    {
        return $this->availableAnalytics;
    }

    /**
     * @param mixed $availableAnalytics
     */
    public function setAvailableAnalytics($availableAnalytics)
    {
        $this->availableAnalytics = $availableAnalytics;
    }

    /**
     * @return mixed
     */
    public function getGuide()
    {
        return $this->guide;
    }

    /**
     * @param mixed $guide
     */
    public function setGuide($guide)
    {
        $this->guide = $guide;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}