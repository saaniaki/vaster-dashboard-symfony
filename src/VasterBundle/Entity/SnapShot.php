<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 04/10/17
 * Time: 3:28 PM
 */

namespace VasterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="VasterBundle\Repository\VasterSnapShotsRepository")
 * @ORM\Table(name="snapshot")
 */
class SnapShot
{
    /**
     * @ORM\ManyToOne(targetEntity="VasterBundle\Entity\User", inversedBy="snapshot")
     * @ORM\JoinColumn(nullable=false, name="userid", referencedColumnName="userid")
     * @ORM\Id
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private $timestamp;

    /**
     * @ORM\Column(type="integer")
     */
    private $seconds;

    /**
     * @ORM\Column(type="boolean")
     */
    private $available;

    /**
     * @ORM\Column(type="datetime")
     */
    private $accountModifiedTime;

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return mixed
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @return mixed
     */
    public function getSeconds()
    {
        return date('Y-m-d H:i', $this->seconds);
    }

    /**
     * @return mixed
     */
    public function getAvailable()
    {
        return $this->available;
    }

    /**
     * @return mixed
     */
    public function getAccountModifiedTime()
    {
        return $this->accountModifiedTime;
    }

}