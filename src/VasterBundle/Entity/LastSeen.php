<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 2017-06-14
 * Time: 12:06 PM
 */

namespace VasterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="VasterBundle\Repository\VasterLastSeenRepository")
 * @ORM\Table(name="ejabberd.last")
 */
class LastSeen
{
    /**
     * @ORM\OneToOne(targetEntity="VasterBundle\Entity\User", inversedBy="lastseen")
     * @ORM\JoinColumn(name="username", referencedColumnName="userid")
     * @ORM\Id
     */
    private $user;

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @ORM\Column(type="integer")
     */
    private $seconds;


    /**
     * @return mixed
     */
    public function getSeconds()
    {
        return date('Y-m-d H:i', $this->seconds);
    }
}