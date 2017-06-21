<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 2017-06-19
 * Time: 4:19 PM
 */

namespace VasterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
 * @ORM\Table(name="languages")
 */
class Language
{
    /**
     * @ORM\OneToOne(targetEntity="VasterBundle\Entity\User", inversedBy="languages")
     * @ORM\JoinColumn(name="userid", referencedColumnName="userid")
     * @ORM\Id
     */
    private $user;

    /**
     * @ORM\Column(type="string")
     */
    private $firstlanguage;

    /**
     * @ORM\Column(type="string")
     */
    private $secondlanguage;

    /**
     * @ORM\Column(type="string")
     */
    private $otherlanguage;

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
    public function getFirstLanguage()
    {
        return $this->firstlanguage;
    }

    /**
     * @param mixed $firstLanguage
     */
    public function setFirstLanguage($firstLanguage)
    {
        $this->firstlanguage = $firstLanguage;
    }

    /**
     * @return mixed
     */
    public function getSecondLanguage()
    {
        return $this->secondlanguage;
    }

    /**
     * @param mixed $secondLanguage
     */
    public function setSecondLanguage($secondLanguage)
    {
        $this->secondlanguage = $secondLanguage;
    }

    /**
     * @return mixed
     */
    public function getOtherLanguage()
    {
        return $this->otherlanguage;
    }

    /**
     * @param mixed $otherLanguage
     */
    public function setOtherLanguage($otherLanguage)
    {
        $this->otherlanguage = $otherLanguage;
    }
}