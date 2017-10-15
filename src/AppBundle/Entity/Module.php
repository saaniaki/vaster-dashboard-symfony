<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 2017-06-06
 * Time: 5:17 PM
 */

namespace AppBundle\Entity;

use AppBundle\Module\Configuration\Configuration;
use AppBundle\Module\Configuration\Settings;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="modules")
 */
class Module
{
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Page", inversedBy="modules")
     * @ORM\JoinColumn(nullable=false)
     */
    private $page;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="bigint", nullable=false)
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * an integer between 1 and 12
     */
    private $size = 6;

    /**
     * @return mixed
     */
    public function getSize()
    {
        //return $this->size;
        if( $this->getConfiguration() == null )
            return 6;
        return $this->getConfiguration()->getLayout()->getSize();
    }

    /**
     * REMOVE THIS IN NEXT VERSION
     * @return mixed
     */
    public function getPostedSize()
    {
        return $this->size;
    }

    /**
     * @param mixed $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * @ORM\Column(type="integer")
     */
    private $rank;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ModuleInfo")
     * @ORM\JoinColumn(nullable=false)
     */
    private $moduleInfo;

    /**
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $configuration;

    /**
     * @return Settings
     */
    public function getConfiguration()
    {
        if( $this->configuration == null )
            return null;
        return new Settings(new ArrayCollection($this->configuration));
        //return new Configuration(new ArrayCollection($this->configuration));
    }

    /**
     * @param mixed $configuration
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @return ModuleInfo
     */
    public function getModuleInfo()
    {
        return $this->moduleInfo;
    }

    /**
     * @param mixed $moduleInfo
     */
    public function setModuleInfo($moduleInfo)
    {
        $this->moduleInfo = $moduleInfo;
    }

    /**
     * @return Page
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param Page $page
     */
    public function setPage($page)
    {
        $this->page = $page;
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
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * @param mixed $rank
     */
    public function setRank($rank)
    {
        $this->rank = $rank;
    }

}