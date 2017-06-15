<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 2017-06-13
 * Time: 4:00 PM
 */

namespace VasterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity(repositoryClass="VasterBundle\Repository\VasterProfessionRepository")
 * @ORM\Table(name="professions")
 */
class Profession
{
    /**
     * @ORM\OneToOne(targetEntity="VasterBundle\Entity\User", inversedBy="profession")
     * @ORM\JoinColumn(name="userid", referencedColumnName="userid")
     * @ORM\Id
     */
    private $user;

    /**
     * @ORM\Column(type="boolean")
     */
    private $available;

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
    public function getAvailable()
    {
        return $this->available;
    }

    /**
     * @param mixed $available
     */
    public function setAvailable($available)
    {
        $this->available = $available;
    }
}