<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 2017-06-13
 * Time: 5:20 PM
 */

namespace VasterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="VasterBundle\Repository\VasterLocationRepository")
 * @ORM\Table(name="locations")
 */
class Location
{
    /**
     * @ORM\ManyToOne(targetEntity="VasterBundle\Entity\User", inversedBy="location")
     * @ORM\JoinColumn(name="userid", referencedColumnName="userid")
     * @ORM\Id
     */
    private $user;

    /**
     * @ORM\Column(type="float")
     */
    private $latitude;

    /**
     * @ORM\Column(type="float")
     */
    private $longitude;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdtime;

    /**
     * @return mixed
     */
    public function getCreatedtime()
    {
        return $this->createdtime;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param mixed $latitude
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    /**
     * @return mixed
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param mixed $longitude
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }
}