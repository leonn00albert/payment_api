<?php

namespace App\Application\Models;

use OpenApi\Annotations as OA;
use Doctrine\ORM\Mapping as ORM;

/**
 * @OA\Schema(
 *     schema="Customer",
 *     required={"name", "email", "balance"},
 *     @OA\Property(property="id", type="integer", format="int64", description="Customer ID"),
 *     @OA\Property(property="name", type="string", description="Customer name"),
 *     @OA\Property(property="email", type="string", format="email", description="Customer email"),
 *     @OA\Property(property="balance", type="number", format="float", description="Customer balance"),
 *     @OA\Property(property="active", type="boolean", description="Customer active status"),
 *     @OA\Property(property="jwt", type="string", description="Customer JWT token"),
 * )
 * @ORM\Entity
 * @ORM\Table(name="customers")
 */
class Customer
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
    private $name;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $balance;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $jwt;

    // Add getters and setters for the properties

    /**
     * Get the ID of the customer.
     *
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set the ID of the customer.
     *
     * @param int $id
     * @return $this
     */
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get the name of the customer.
     *
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set the name of the customer.
     *
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get the balance of the customer.
     *
     * @return float
     */
    public function getBalance(): ?float
    {
        return $this->balance;
    }

    /**
     * Set the balance of the customer.
     *
     * @param float $balance
     * @return $this
     */
    public function setBalance(float $balance): self
    {
        $this->balance = $balance;
        return $this;
    }

    /**
     * Get the email of the customer.
     *
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Set the email of the customer.
     *
     * @param string $email
     * @return $this
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Check if the customer is active.
     *
     * @return bool
     */
    public function isActive(): ?bool
    {
        return $this->active;
    }

    /**
     * Set the active status of the customer.
     *
     * @param bool $active
     * @return $this
     */
    public function setActive(bool $active): self
    {
        $this->active = $active;
        return $this;
    }

    /**
     * Get the JWT of the customer.
     *
     * @return string
     */
    public function getJwt(): ?string
    {
        return $this->jwt;
    }

    /**
     * Set the JWT of the customer.
     *
     * @param string $jwt
     * @return $this
     */
    public function setJwt(string $jwt): self
    {
        $this->jwt = $jwt;
        return $this;
    }
    /**
     * Converts the class to an array.
     *
     * @return array An associative array representing the class properties.
     */
    public function toArray(): array
    {
        return [
            "id" => $this->id,
            "email" => $this->email,
            "balance" => $this->balance,
            "name" => $this->name,
            "active" => $this->active,

        ];
    }
}
