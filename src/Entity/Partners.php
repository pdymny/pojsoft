<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="partners")
 * @ORM\Entity
 */
class Partners
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=24)
     */
    private $code;

    /**
     * @ORM\Column(type="integer")
     */
    private $percent;

    /**
     * @ORM\Column(type="decimal", scale=2)
     */
    private $amount;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="userInvoices")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PartnersHistory", mappedBy="partner", cascade={"remove"})
     * @ORM\JoinColumn(nullable=false)
     * @ORM\OrderBy({"date" = "DESC"})
     */
    private $history;


    public function __construct()
    {
        $this->history = new ArrayCollection();
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

    public function getPercent(): ?int
    {
        return $this->percent;
    }

    public function setPercent(int $percent): self
    {
        $this->percent = $percent;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

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
     * @return Collection|PartnersHisotry[]
     */
    public function getHistory()
    {
        return $this->history;
    }

    public function setHistory(?PartnersHistory $history): self
    {
        $this->history = $history;

        return $this;
    }

}
