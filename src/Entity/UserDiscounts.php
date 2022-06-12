<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="user_discounts")
 * @ORM\Entity
 */
class UserDiscounts
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="decimal", scale=2)
     */
    private $money;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="discount")
     */
    private $user;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\UserInvoices", inversedBy="discounts")
     */
    private $invoice;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Discounts", inversedBy="userDiscounts")
     */
    private $discounts;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMoney(): ?float
    {
        return $this->money;
    }

    public function setMoney(float $money): self
    {
        $this->money = $money;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getInvoice(): ?UserInvoices
    {
        return $this->invoice;
    }

    public function setInvoice(?UserInvoices $invoice): self
    {
        $this->invoice = $invoice;
        return $this;
    }

    public function getDiscounts(): ?Discounts
    {
        return $this->discounts;
    }

    public function setDiscounts(?Discounts $discounts): self
    {
        $this->discounts = $discounts;
        return $this;
    }

}
