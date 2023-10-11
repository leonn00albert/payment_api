<?php

namespace App\Application\Models;

use OpenApi\Annotations as OA;
use Doctrine\ORM\Mapping as ORM;

/**
 * @OA\Schema(
 *     schema="Method",
 *     required={"id", "name", "description", "active"},
 *     @OA\Property(property="id", type="integer", format="int64", description="Method ID"),
 *     @OA\Property(property="name", type="string", description="Method name"),
 *     @OA\Property(property="description", type="string", description="Method description"),
 *     @OA\Property(property="active", type="boolean", description="Method activity status"),
 * )
 * @ORM\Entity
 * @ORM\Table(name="payment_methods")
 */
class Method
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
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * Get the ID of the payment method.
     *
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set the ID of the payment method.
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
     * Get the name of the payment method.
     *
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set the name of the payment method.
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
     * Get the description of the payment method.
     *
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set the description of the payment method.
     *
     * @param string $description
     * @return $this
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;
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
     * Converts the class to an array.
     *
     * @return array An associative array representing the class properties.
     */
    public function toArray(): array
    {
        return [
            "id" => $this->id,
            "description" => $this->description,
            "active" => $this->active,
            "name" => $this->name,
        ];
    }
}
