<?php

namespace Bdloc\AppBundle\Entity;

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
     *
     * @ORM\Column(name="paypalId", type="string", length=255)
     */
    private $paypalId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="valindUntil", type="date")
     */
    private $valindUntil;

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
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;


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
     * Set valindUntil
     *
     * @param \DateTime $valindUntil
     * @return CreditCard
     */
    public function setValindUntil($valindUntil)
    {
        $this->valindUntil = $valindUntil;

        return $this;
    }

    /**
     * Get valindUntil
     *
     * @return \DateTime 
     */
    public function getValindUntil()
    {
        return $this->valindUntil;
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
     * Set userId
     *
     * @param integer $userId
     * @return CreditCard
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->userId;
    }
}
