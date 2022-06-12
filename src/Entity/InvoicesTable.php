<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="invoices_table")
 * @ORM\Entity
 */
class InvoicesTable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @ORM\Column(type="decimal", scale=2)
     */
    private $cost;

    /**
     * @ORM\Column(type="integer")
     */
    private $lot;

    /**
     * @ORM\Column(type="integer")
     */
    private $tax;

    /**
     * @ORM\Column(type="decimal", scale=2)
     */
    private $money;

    /**
     * @ORM\Column(type="string")
     */
    private $data;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\UserInvoices", inversedBy="InvoicesTable")
     */
    private $invoice;


    public function getId(): ?int
    {
        return $this->id;
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

    public function getCost(): ?float
    {
        return $this->cost;
    }

    public function setCost(float $value): self
    {
        $this->cost = $value;

        return $this;
    }

    public function getLot(): ?int
    {
        return $this->lot;
    }

    public function setLot(int $lot): self
    {
        $this->lot = $lot;

        return $this;
    }

    public function getTax(): ?int
    {
        return $this->tax;
    }

    public function setTax(int $tax): self
    {
        $this->tax = $tax;

        return $this;
    }

    public function getMoney(): ?float
    {
        return $this->money;
    }

    public function setMoney(float $value): self
    {
        $this->money = $value;

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

    public function getData(): ?string
    {
        return $this->data;
    }

    public function setData(string $data): self
    {
        $this->data = $data;

        return $this;
    }

}
