<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 2017-06-20
 * Time: 1:50 PM
 */

namespace VasterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity(repositoryClass="VasterBundle\Repository\VasterSearchRepository")
 * @ORM\Table(name="search_history")
 */
class Search
{
    /**
     * @ORM\ManyToOne(targetEntity="VasterBundle\Entity\User", inversedBy="searches")
     * @ORM\JoinColumn(nullable=false, name="userid", referencedColumnName="userid")
     * @ORM\Id
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdtime;

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
    public function getCreatedtime()
    {
        return $this->createdtime;
    }

    /**
     * @param mixed $createdtime
     */
    public function setCreatedtime($createdtime)
    {
        $this->createdtime = $createdtime;
    }

    /**
     * @return mixed
     */
    public function getSearchquery()
    {
        return $this->searchquery;
    }

    /**
     * @param mixed $searchquery
     */
    public function setSearchquery($searchquery)
    {
        $this->searchquery = $searchquery;
    }

    /**
     * @return mixed
     */
    public function getSearchresult()
    {
        return $this->searchresult;
    }

    /**
     * @param mixed $searchresult
     */
    public function setSearchresult($searchresult)
    {
        $this->searchresult = $searchresult;
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

    /**
     * @ORM\Column(type="string")
     */
    private $searchquery;

    /**
     * @ORM\Column(type="string")
     */
    private $searchresult;

    /**
     * @ORM\Column(type="float")
     */
    private $latitude;

    /**
     * @ORM\Column(type="float")
     */
    private $longitude;
}