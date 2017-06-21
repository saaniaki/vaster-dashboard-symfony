<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 2017-06-20
 * Time: 9:12 AM
 */

namespace VasterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
 * @ORM\Table(name="socialnetwork")
 */
class SocialNetwork
{
    /**
     * @ORM\ManyToOne(targetEntity="VasterBundle\Entity\User", inversedBy="socialNetwork")
     * @ORM\JoinColumn(name="userid", referencedColumnName="userid")
     * @ORM\Id
     */
    private $user;

    /**
     * @ORM\Column(type="string")
     */
    private $type;

    /**
     * @ORM\Column(type="string")
     */
    private $url;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     */
    private $socialid;

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

    /**
     * @return mixed
     */
    public function getSocialid()
    {
        return $this->socialid;
    }

    /**
     * @param mixed $socialid
     */
    public function setSocialid($socialid)
    {
        $this->socialid = $socialid;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
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
}