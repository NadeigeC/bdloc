<?php

namespace Bdloc\AppBundle\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;

//http://benjamin.leveque.me/symfony2-validation-groups.html

/**
 * User
 *
 * @UniqueEntity("username", message="Ce pseudo est déjà utilisé !", groups={"registration"})
 * @UniqueEntity("email", message="Vous avez déjà un compte ici !", groups={"registration"})
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="Bdloc\AppBundle\Entity\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @Assert\NotBlank(message="Veuillez entrer un pseudo", groups={"registration","updateProfile"})
     * @ORM\Column(name="username", type="string", length=255)
     */
    private $username;

    /**
     * @var string
     * @Assert\NotBlank(message="Veuillez entrer un email", groups={"registration","forgotPassword","updateProfile"}  )
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var string
     * @Assert\NotBlank(message="Veuillez entrer un mot de passe", groups={"registration","newPassword","updatePassword"})
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=255)
     */
    private $token;

    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="string", length=255)
     */
    private $salt;

    /**
     * @var array
     *
     * @ORM\Column(name="roles", type="array")
     */
    private $roles;

    /**
     * @var string
     * @Assert\NotBlank(message="Veuillez entrer votre prénom", groups={"registration","updateProfile"})
     * @ORM\Column(name="firstName", type="string", length=255)
     */
    private $firstName;

    /**
     * @var string
     * @Assert\NotBlank(message="Veuillez votre nom", groups={"registration","updateProfile"})
     * @ORM\Column(name="lastName", type="string", length=255)
     */
    private $lastName;

    /**
     * @var string
     * @Assert\NotBlank(message="Merci d'indiquer votre adresse postale", groups={"registration","updateProfile"})
     * @ORM\Column(name="adress", type="string", length=255)
     */
    private $adress;

    /**
     * @var string
     * @Assert\Regex(
     *           pattern= "/^0[1-9]([-. ]?[0-9]{2}){4}$/",
     *           message= "Entrez un numero valide (10 chiffres avec ou sans espaces)",
     *           groups={"registration"}, groups={"updateProfile"})
     * @Assert\NotBlank(message="Veuillez entrer un numéro de téléphone", groups={"registration","updateProfile"})
     * @ORM\Column(name="phone", type="string", length=20)
     */
    private $phone;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isActive", type="boolean")
     */
    private $isActive;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreated", type="datetime")
     */
    private $dateCreated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateModified", type="datetime")
     */
    private $dateModified;

     /**
    *
    *@ORM\ManyToOne(targetEntity="DropSpot", inversedBy="users")
    */
    private $dropSpot;


     /**
    *
    *@ORM\OneToMany(targetEntity="Cart", mappedBy="user")
    */
    private $carts;

     /**
    *
    *@ORM\OneToMany(targetEntity="Fine", mappedBy="user")
    */
    private $fines;


    /**
    *
    *@ORM\OneToOne(targetEntity="CreditCard", mappedBy="user")
    */
    private $creditCard;



    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set token
     *
     * @param string $token
     * @return User
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set roles
     *
     * @param array $roles
     * @return User
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get roles
     *
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }


    /**
     * Set adress
     *
     * @param string $adress
     * @return User
     */
    public function setAdress($adress)
    {
        $this->adress = $adress;

        return $this;
    }

    /**
     * Get adress
     *
     * @return string
     */
    public function getAdress()
    {
        return $this->adress;
    }

    /**
     * Set phone
     *
     * @param string $phone
     * @return User
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     * @return User
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Set dateModified
     *
     * @param \DateTime $dateModified
     * @return User
     */
    public function setDateModified($dateModified)
    {
        $this->dateModified = $dateModified;

        return $this;
    }

    /**
     * Get dateModified
     *
     * @return \DateTime
     */
    public function getDateModified()
    {
        return $this->dateModified;
    }



    public function eraseCredentials(){
        //$this->password = null;
    }

    /**
     * @ORM\PrePersist
     */
    public function beforeInsert(){
            $this->setDateCreated( new \DateTime() );
            $this->setDateModified( new \DateTime() );
        }

    /**
     * @ORM\PreUpdate
     */
    public function beforeEdit(){
            $this->setDateModified( new \DateTime() );
        }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->carts = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add carts
     *
     * @param \Bdloc\AppBundle\Entity\Cart $carts
     * @return User
     */
    public function addCart(\Bdloc\AppBundle\Entity\Cart $carts)
    {
        $this->carts[] = $carts;

        return $this;
    }

    /**
     * Remove carts
     *
     * @param \Bdloc\AppBundle\Entity\Cart $carts
     */
    public function removeCart(\Bdloc\AppBundle\Entity\Cart $carts)
    {
        $this->carts->removeElement($carts);
    }

    /**
     * Get carts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCarts()
    {
        return $this->carts;
    }

    /**
     * Add fines
     *
     * @param \Bdloc\AppBundle\Entity\Fine $fines
     * @return User
     */
    public function addFine(\Bdloc\AppBundle\Entity\Fine $fines)
    {
        $this->fines[] = $fines;

        return $this;
    }

    /**
     * Remove fines
     *
     * @param \Bdloc\AppBundle\Entity\Fine $fines
     */
    public function removeFine(\Bdloc\AppBundle\Entity\Fine $fines)
    {
        $this->fines->removeElement($fines);
    }

    /**
     * Get fines
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFines()
    {
        return $this->fines;
    }







    /**
     * Set dropSpot
     *
     * @param \Bdloc\AppBundle\Entity\DropSpot $dropSpot
     * @return User
     */
    public function setDropSpot(\Bdloc\AppBundle\Entity\DropSpot $dropSpot = null)
    {
        $this->dropSpot = $dropSpot;

        return $this;
    }

    /**
     * Get dropSpot
     *
     * @return \Bdloc\AppBundle\Entity\DropSpot
     */
    public function getDropSpot()
    {
        return $this->dropSpot;
    }

    /**
     * Set creditCard
     *
     * @param \Bdloc\AppBundle\Entity\CreditCard $creditCard
     * @return User
     */
    public function setCreditCard(\Bdloc\AppBundle\Entity\CreditCard $creditCard = null)
    {
        $this->creditCard = $creditCard;

        return $this;
    }

    /**
     * Get creditCard
     *
     * @return \Bdloc\AppBundle\Entity\CreditCard
     */
    public function getCreditCard()
    {
        return $this->creditCard;
    }
}
