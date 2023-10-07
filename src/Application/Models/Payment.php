<?php
// src/Entity/Payment.php
namespace App\Application\Models;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="payments")
 */
class Payment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $amount;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="integer")
     * @ORM\JoinColumn(name="from_customer_id", referencedColumnName="id")
     * @ORM\ManyToOne(targetEntity="Customer")
     */
    private $fromCustomer;

    /**
     * @ORM\Column(type="integer")
     * @ORM\JoinColumn(name="to_customer_id", referencedColumnName="id")
     * @ORM\ManyToOne(targetEntity="Customer")
     */
    private $toCustomer;

    // Getters and setters for properties



    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     */
    public function setId($id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the value of description
     */
    public function setDescription($description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the value of amount
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set the value of amount
     */
    public function setAmount($amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get the value of createdAt
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set the value of createdAt
     */
    public function setCreatedAt($createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get the value of fromCustomer
     */
    public function getFromCustomer()
    {
        return $this->fromCustomer;
    }

    /**
     * Set the value of fromCustomer
     */
    public function setFromCustomer($fromCustomer): self
    {
        $this->fromCustomer = $fromCustomer;

        return $this;
    }

    /**
     * Get the value of toCustomer
     */
    public function getToCustomer()
    {
        return $this->toCustomer;
    }

    /**
     * Set the value of toCustomer
     */
    public function setToCustomer($toCustomer): self
    {
        $this->toCustomer = $toCustomer;

        return $this;
    }
}
