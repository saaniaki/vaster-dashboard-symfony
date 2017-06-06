<?php
/**
 * Created by PhpStorm.
 * User: saaniaki
 * Date: 2017-06-02
 * Time: 4:44 PM
 */

namespace AppBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="users") //I just chaged this
 * @UniqueEntity(fields={"email"}, message="This user has been registered before!")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint", nullable=false)
     */
    private $id;

    /**
     * @Assert\NotBlank(message = "Oops! Email is required!")
     * @Assert\Email(message = "Oops! {{ value }} is not a valid email.")
     * @ORM\Column(type="string", unique=true)
     */
    private $email;

    /**
     * The encoded password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @Assert\NotBlank(groups={"Registration"}, message = "Come on! Password is required!")
     * @Assert\Length(
     *      min = 6,
     *      max = 12,
     *      minMessage = "Your password must be at least {{ limit }} characters long!",
     *      maxMessage = "Your password cannot be longer than {{ limit }} characters!"
     * )
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="json_array")
     */
    private $roles = [];

    /**
     * @Assert\Length(
     *      min = 11,
     *      max = 11,
     *      exactMessage = "Phone number must contain {{ limit }} digits!"
     * )
     */
    private $phone;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Page", mappedBy="user")
     * @ORM\OrderBy({"rank"="DESC"})
     */
    private $pages;


    function __construct()
    {
        $this->pages = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }


    public function getUsername()
    {
        return $this->email;
    }

    public function getRoles()
    {
        $roles = $this->roles;
        // give everyone ROLE_USER!
        if (!in_array('ROLE_USER', $roles)) {
            $roles[] = 'ROLE_USER';
        }
        return $roles;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getSalt()
    {
    }

    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param mixed $plainPassword
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
        $this->password = null;
    }

    public function setRoles(array $roles)
    {
        $this->roles = $roles;
    }

    /**
     * @return ArrayCollection|Page[]
     */
    public function getPages()
    {
        return $this->pages;
    }



}