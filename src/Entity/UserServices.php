<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="user_services")
 * @ORM\Entity
 */
class UserServices
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $pack;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_start_pack;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_end_pack;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_delete_host;

    /**
     * @ORM\Column(type="string")
     */
    private $host;  

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="userServices")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Services", inversedBy="userServices")
     * @ORM\JoinColumn(nullable=false)
     */
    private $myServices;


    public function __construct()
    {
        $this->myServices = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPack()
    {
        switch($this->pack) {
            case 1: $pack = 'MINI'; break;
            case 2: $pack = 'MEDIUM'; break;
            case 3: $pack = 'MAXI'; break;
            case 4: $pack = 'PRO'; break;
        }

        return $pack;
    }

    public function setPack(int $pack): self
    {
        $this->pack = $pack;

        return $this;
    }

    public function getPackInt(): ?int
    {
        return $this->pack;
    }

    public function setPackInt(int $pack): self
    {
        $this->pack = $pack;

        return $this;
    }

    public function getDateStartPack(): ?\DateTimeInterface
    {
        return $this->date_start_pack;
    }

    public function setDateStartPack(\DateTimeInterface $date): self
    {
        $this->date_start_pack = $date;

        return $this;
    }

    public function getDateEndPack(): ?\DateTimeInterface
    {
        return $this->date_end_pack;
    }

    public function setDateEndPack(\DateTimeInterface $date): self
    {
        $this->date_end_pack = $date;

        return $this;
    }

    public function getDateDeleteHost(): ?\DateTimeInterface
    {
        return $this->date_delete_host;
    }

    public function setDateDeleteHost(\DateTimeInterface $date): self
    {
        $this->date_delete_host = $date;

        return $this;
    }

    public function getHost(): ?string
    {
        return $this->host;
    }

    public function setHost(string $host): self
    {
        $this->host = $host;

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

    public function getMyServices(): ?Services
    {
        return $this->myServices;
    }

    public function setMyServices(?Services $service): self
    {
        $this->myServices = $service;

        return $this;
    }

    public function getOrder() 
    {
        $table = array();

        $table['what'] = strtolower($this->myServices->getName());
        $table['pack'] = $this->getPack();
        $table['id'] = $this->id;

        return $table;
    }

}
