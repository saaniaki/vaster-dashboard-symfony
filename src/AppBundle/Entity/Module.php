<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 2017-06-06
 * Time: 5:17 PM
 */

namespace AppBundle\Entity;

use AppBundle\Module\Configuration\Configuration;
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
     * @ORM\Column(type="string", nullable=true)
     */
    private $analytics;

    /**
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $userType;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $availability;

    /**
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $configuration;

    /**
     * @return Configuration
     */
    public function getConfiguration()
    {
        if( $this->configuration == null )
            return null;
        return new Configuration(new ArrayCollection($this->configuration));
    }

    /**
     * @param mixed $configuration
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @return mixed
     */
    public function getAvailability()
    {
        return $this->availability;
    }

    /**
     * @param mixed $availability
     */
    public function setAvailability($availability)
    {
        $this->availability = $availability;
    }

    /**
     * @return mixed
     */
    public function getDeviceType()
    {
        return $this->deviceType;
    }

    /**
     * @param mixed $deviceType
     */
    public function setDeviceType($deviceType)
    {
        $this->deviceType = $deviceType;
    }

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $deviceType;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $keyword;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fromDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $toDate;

    /**
     * @return mixed
     */
    public function getFromDate()
    {
        return $this->fromDate;
    }

    /**
     * @param mixed $fromDate
     */
    public function setFromDate($fromDate)
    {
        $this->fromDate = $fromDate;
    }

    /**
     * @return mixed
     */
    public function getToDate()
    {
        return $this->toDate;
    }

    /**
     * @param mixed $toDate
     */
    public function setToDate($toDate)
    {
        $this->toDate = $toDate;
    }

    /**
     * @return mixed
     */
    public function getAnalytics()
    {
        return $this->analytics;
    }

    /**
     * @param mixed $analytics
     */
    public function setAnalytics($analytics)
    {
        $this->analytics = $analytics;
    }

    /**
     * @return mixed
     */
    public function getUserType()
    {
        return $this->userType;
    }

    /**
     * @param mixed $userType
     */
    public function setUserType($userType)
    {
        $this->userType = $userType;
    }

    /**
     * @return mixed
     */
    public function getKeyword()
    {
        return $this->keyword;
    }

    /**
     * @param mixed $keyword
     */
    public function setKeyword($keyword)
    {
        $this->keyword = $keyword;
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