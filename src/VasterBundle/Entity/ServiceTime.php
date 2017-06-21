<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 2017-06-20
 * Time: 9:59 AM
 */

namespace VasterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
 * @ORM\Table(name="servicetime")
 */
class ServiceTime
{
    /**
     * @ORM\ManyToOne(targetEntity="VasterBundle\Entity\User", inversedBy="serviceTime")
     * @ORM\JoinColumn(nullable=false, name="userid", referencedColumnName="userid")
     */
    private $user;

    /**
     * @ORM\Id
     * @ORM\Column(type="bigint", nullable=false)
     */
    private $servicetimeid;

    /**
     * @ORM\Column(type="string")
     */
    private $weekdays;

    /**
     * @ORM\Column(type="string")
     */
    private $starttime;

    /**
     * @ORM\Column(type="string")
     */
    private $endtime;

    /**
     * @ORM\Column(type="boolean")
     */
    private $availability;

    /**
     * @return mixed
     */
    public function getServicetimeId()
    {
        return $this->servicetimeid;
    }

    /**
     * @param mixed $servicetimeId
     */
    public function setServicetimeId($servicetimeId)
    {
        $this->servicetimeid = $servicetimeId;
    }

    /**
     * @return mixed
     */
    public function getStarttime()
    {
        return $this->starttime;
    }

    /**
     * @param mixed $starttime
     */
    public function setStarttime($starttime)
    {
        $this->starttime = $starttime;
    }

    /**
     * @return mixed
     */
    public function getEndtime()
    {
        return $this->endtime;
    }

    /**
     * @param mixed $endtime
     */
    public function setEndtime($endtime)
    {
        $this->endtime = $endtime;
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
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getWeekdays()
    {
        return $this->weekdays;
    }

    /**
     * @param mixed $weekdays
     */
    public function setWeekdays($weekdays)
    {
        $this->weekdays = $weekdays;
    }
}