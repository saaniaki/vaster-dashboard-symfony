<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 2017-06-06
 * Time: 10:46 AM
 */

namespace VasterBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="VasterBundle\Repository\VasterUserRepository")
 * @ORM\Table(name="users")
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     */
    private $userid;

    /**
     * @ORM\Column(type="string")
     */
    private $email;

    /**
     * @ORM\Column(type="bigint", unique=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="string")
     */
    private $firstname;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdtime;

    /**
     * @ORM\OneToOne(targetEntity="VasterBundle\Entity\Profession", mappedBy="user")
     */
    private $profession;

    /**
     * @ORM\OneToOne(targetEntity="VasterBundle\Entity\Account", mappedBy="user")
     */
    private $account;

    /**
     * @ORM\OneToMany(targetEntity="VasterBundle\Entity\Location", mappedBy="user")
     * @ORM\OrderBy({"createdtime"="DESC"})
     */
    private $location;

    /**
     * @ORM\OneToOne(targetEntity="VasterBundle\Entity\LastSeen", mappedBy="user")
     */
    private $lastseen;

    /**
     * @return LastSeen
     */
    public function getLastseen()
    {
        return $this->lastseen;
    }

    /**
     * @return ArrayCollection|Location[]
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @return ArrayCollection|Account[]
     */
    public function getAccount()
    {
        return $this->account;
    }

    function __construct()
    {
        $this->profession = new ArrayCollection();
        $this->account = new ArrayCollection();
        $this->location = new ArrayCollection();
    }

    /**
     * @return ArrayCollection|Profession[]
     */
    public function getProfession()
    {
        return $this->profession;
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
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @return mixed
     */
    public function getLastname()
    {
        return $this->lastname;
    }
    /**
     * @ORM\Column(type="string")
     */
    private $lastname;

    /**
     * @ORM\Column(type="string")
     */
    private $accounttype;

    /**
     * @return mixed
     */
    public function getAccounttype()
    {
        return $this->accounttype;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userid;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }


}