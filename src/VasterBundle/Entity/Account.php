<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 2017-06-13
 * Time: 4:52 PM
 */

namespace VasterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="VasterBundle\Repository\VasterAccountRepository")
 * @ORM\Table(name="accounts")
 */
class Account
{
    /**
     * @ORM\OneToOne(targetEntity="VasterBundle\Entity\User", inversedBy="account")
     * @ORM\JoinColumn(name="userid", referencedColumnName="userid")
     * @ORM\Id
     */
    private $user;

    /**
     * @ORM\Column(type="string")
     */
    private $devicetype;

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
    public function getDeviceType()
    {
        return $this->devicetype;
    }

    /**
     * @param mixed $deviceType
     */
    public function setDeviceType($devicetype)
    {
        $this->devicetype = $devicetype;
    }
}