<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="discounts")
 * @ORM\Entity
 */
class Discounts
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $code;

    /**
     * @ORM\Column(type="integer")
     */
    private $discount;

    /**
     * @ORM\Column(type="datetime")
     */
    private $term;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\UserDiscounts", mappedBy="discounts")
     */
    private $userDiscounts;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\UserInvoices", mappedBy="disc")
     */
    private $invoiceDiscounts;


    public function __toString()
    {
        return (string) $this->code;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getDiscount(): ?int
    {
        return $this->discount;
    }

    public function setDiscount(int $discount): self
    {
        $this->discount = $discount;

        return $this;
    }

    public function getTerm(): ?\DateTimeInterface
    {
        return $this->term;
    }

    public function setTerm(\DateTimeInterface $date): self
    {
        $this->term = $date;

        return $this;
    }

}
