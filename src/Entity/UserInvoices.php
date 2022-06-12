<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="user_invoices")
 * @ORM\Entity
 */
class UserInvoices
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=250)
     */
    private $address_company;

    /**
     * @ORM\Column(type="string", length=250)
     */
    private $address_my;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_issued;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_payment;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date_paid;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="userInvoices")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\InvoicesTable", mappedBy="invoice", cascade={"remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $InvoicesTable;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\UserDiscounts", mappedBy="invoice")
     */
    private $discounts;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Discounts", inversedBy="invoiceDiscounts")
     */
    private $disc;


    public function __construct()
    {
        $this->InvoicesTable = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAddressCompany(): ?string
    {
        return $this->address_company;
    }

    public function setAddressCompany(string $address): self
    {
        $this->address_company = $address;

        return $this;
    }

    public function getAddressMy(): ?string
    {
        return $this->address_my;
    }

    public function setAddressMy(string $address): self
    {
        $this->address_my = $address;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDateIssued(): ?\DateTimeInterface
    {
        return $this->date_issued;
    }

    public function setDateIssued(\DateTimeInterface $date): self
    {
        $this->date_issued = $date;

        return $this;
    }

    public function getDatePayment(): ?\DateTimeInterface
    {
        return $this->date_payment;
    }

    public function setDatePayment(\DateTimeInterface $date): self
    {
        $this->date_payment = $date;

        return $this;
    }

    public function getDatePaid()
    {
        return $this->date_paid;
    }

    public function setDatePaid($date)
    {
        $this->date_paid = $date;

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

    /**
     * @return Collection|InvoicesTable[]
     */
    public function getInvoicesTable()
    {
        return $this->InvoicesTable;
    }

    public function getDisc(): ?Discounts
    {
        return $this->disc;
    }

    public function setDisc(?Discounts $discounts): self
    {
        $this->disc = $discounts;
        return $this;
    }
}
