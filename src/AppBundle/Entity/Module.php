<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 2017-06-06
 * Time: 5:17 PM
 */

namespace AppBundle\Entity;

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
     * @ORM\Column(type="string", nullable=true)
     */
    private $userType;

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