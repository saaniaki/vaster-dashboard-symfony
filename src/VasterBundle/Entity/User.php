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
     * @ORM\Column(type="float")
     */
    private $balance;

    /**
     * @ORM\Column(type="string")
     */
    private $urlprofile;

    /**
     * @ORM\Column(type="string")
     */
    private $urlcover;

    /**
     * @return mixed
     */
    public function getUrlcover()
    {
        return $this->urlcover;
    }

    /**
     * @param mixed $urlcover
     */
    public function setUrlcover($urlcover)
    {
        $this->urlcover = $urlcover;
    }

    /**
     * @return mixed
     */
    public function getUrlprofile()
    {
        return $this->urlprofile;
    }

    /**
     * @param mixed $urlprofile
     */
    public function setUrlprofile($urlprofile)
    {
        $this->urlprofile = $urlprofile;
    }

    /**
     * @return mixed
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * @param mixed $balance
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;
    }

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
     * @ORM\OneToOne(targetEntity="VasterBundle\Entity\Language", mappedBy="user")
     */
    private $languages;

    /**
     * @ORM\OneToMany(targetEntity="VasterBundle\Entity\SocialNetwork", mappedBy="user")
     */
    private $socialNetwork;

    /**
     * @ORM\OneToMany(targetEntity="VasterBundle\Entity\ServiceTime", mappedBy="user")
     * @ORM\OrderBy({"servicetimeid"="ASC"})
     */
    private $serviceTime;

    /**
     * @ORM\OneToMany(targetEntity="VasterBundle\Entity\Search", mappedBy="user")
     * @ORM\OrderBy({"createdtime"="ASC"})
     */
    private $searches;

    /**
     * @return mixed
     */
    public function getSearches()
    {
        return $this->searches;
    }

    /**
     * @param mixed $searches
     */
    public function setSearches($searches)
    {
        $this->searches = $searches;
    }

    /**
     * @ORM\OneToMany(targetEntity="VasterBundle\Entity\SnapShot", mappedBy="user")
     */
    private $snapshots;

    /**
     * @return mixed
     */
    public function getSnapshots()
    {
        return $this->snapshots;
    }

    /**
     * @param mixed $snapshots
     */
    public function setSnapshots($snapshots)
    {
        $this->snapshots = $snapshots;
    }

    /**
     * @return ArrayCollection|ServiceTime[]
     */
    public function getServiceTime()
    {
        return $this->serviceTime;
    }

    /**
     * @param mixed $serviceTime
     */
    public function setServiceTime($serviceTime)
    {
        $this->serviceTime = $serviceTime;
    }

    /**
     * @return ArrayCollection|SocialNetwork[]
     */
    public function getSocialNetwork()
    {
        return $this->socialNetwork;
    }

    /**
     * @param mixed $socialNetwork
     */
    public function setSocialNetwork($socialNetwork)
    {
        $this->socialNetwork = $socialNetwork;
    }

    /**
     * @return Language
     */
    public function getLanguages()
    {
        return $this->languages;
    }

    /**
     * @param mixed $languages
     */
    public function setLanguages($languages)
    {
        $this->languages = $languages;
    }

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
     * @return Account
     */
    public function getAccount()
    {
        return $this->account;
    }

    function __construct()
    {
        $this->location = new ArrayCollection();
        $this->socialNetwork = new ArrayCollection();
        $this->serviceTime = new ArrayCollection();
    }

    /**
     * @return Profession
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
     * @param mixed $accounttype
     */
    public function setAccounttype($accounttype)
    {
        $this->accounttype = $accounttype;
    }

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