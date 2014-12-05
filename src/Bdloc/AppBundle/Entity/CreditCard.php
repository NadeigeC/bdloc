<?php

namespace Bdloc\AppBundle\Entity;

use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * CreditCard
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Bdloc\AppBundle\Entity\CreditCardRepository")
 */
class CreditCard
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
     * @Assert\NotBlank(message="Veuillez entrer numero de carte de crédit", groups={"creditCard"}, groups={"updateCreditCard"})
     * @ORM\Column(name="creditCardType", type="string", length=255)
     */
    private $creditCardType;

    /**
     * @var string
     * @Assert\NotBlank(message="Veuillez entrer numero de carte de crédit", groups={"creditCard"}, groups={"updateCreditCard"})
     * @ORM\Column(name="paypalId", type="string", length=255)
     */
    private $paypalId;

    /**
     * @var string
     * @Assert\Regex(
     *           pattern= "/^[0-9]{3,3}$/",
     *           message= "Entrez un cryptogramme valide (3 chiffres sans espaces)",
     *           groups={"registration"}, groups={"updateProfile"})
     * @Assert\NotBlank(message="Veuillez entrer votre identifiant Paypal", groups={"creditCard"}, groups={"updateCreditCard"})
     * @ORM\Column(name="cryptoCard", type="string", length=3)
     */
    private $cryptoCard;


    /**
     * @var string
     * @Assert\NotBlank(message="Merci de renseigner le propriétaire de la carte de crédit", groups={"creditCard"}, groups={"updateCreditCard"})
     * @ORM\Column(name="ownerIdentity", type="string", length=255)
     */
    private $ownerIdentity;


    /**
     * @var \DateTime
     * @Assert\NotBlank(message="Merci de renseigner la date d'expiration", groups={"creditCard"}, groups={"updateCreditCard"})
     * @ORM\Column(name="validUntil", type="date")
     */
    private $validUntil;

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
    *@ORM\OneToOne(targetEntity="User", inversedBy="creditCard")
    */
    private $user;


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
     * Set paypalId
     *
     * @param string $paypalId
     * @return CreditCard
     */
    public function setPaypalId($paypalId)
    {
        $this->paypalId = $paypalId;

        return $this;
    }

    /**
     * Get paypalId
     *
     * @return string
     */
    public function getPaypalId()
    {
        return $this->paypalId;
    }

    /**
     * Set validUntil
     *
     * @param \DateTime $validUntil
     * @return CreditCard
     */
    public function setValidUntil($validUntil)
    {
        $this->validUntil = $validUntil;

        return $this;
    }

    /**
     * Get validUntil
     *
     * @return \DateTime
     */
    public function getValidUntil()
    {
        return $this->validUntil;
    }

    /**
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     * @return CreditCard
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
     * @return CreditCard
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


    /**
     * Set user
     *
     * @param \Bdloc\AppBundle\Entity\User $user
     * @return CreditCard
     */
    public function setUser(\Bdloc\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Bdloc\AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set creditCardType
     *
     * @param string $creditCardType
     * @return CreditCard
     */
    public function setCreditCardType($creditCardType)
    {
        $this->creditCardType = $creditCardType;

        return $this;
    }

    /**
     * Get creditCardType
     *
     * @return string
     */
    public function getCreditCardType()
    {
        return $this->creditCardType;
    }

    /**
     * Set cryptoCard
     *
     * @param string $cryptoCard
     * @return CreditCard
     */
    public function setCryptoCard($cryptoCard)
    {
        $this->cryptoCard = $cryptoCard;

        return $this;
    }

    /**
     * Get cryptoCard
     *
     * @return string
     */
    public function getCryptoCard()
    {
        return $this->cryptoCard;
    }

    /**
     * Set ownerIdentity
     *
     * @param string $ownerIdentity
     * @return CreditCard
     */
    public function setOwnerIdentity($ownerIdentity)
    {
        $this->ownerIdentity = $ownerIdentity;

        return $this;
    }

    /**
     * Get ownerIdentity
     *
     * @return string
     */
    public function getOwnerIdentity()
    {
        return $this->ownerIdentity;
    }
}
