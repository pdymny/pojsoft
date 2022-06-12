<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="services")
 * @ORM\Entity
 */
class Services
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=125)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $path;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $icon;

    /**
     * @ORM\Column(type="string")
     */
    private $data_pack;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserServices", mappedBy="myServices", cascade={"remove"})
     */
    private $userServices;


    public function __toString()
    {
        return (string) $this->name;
    }

    public function __construct()
    {
        $this->userServices = new ArrayCollection();
    }

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function getDataPack(): ?string
    {
        return $this->data_pack;
    }

    public function setDataPack(string $data_pack): self
    {
        $this->data_pack = $data_pack;

        return $this;
    }

    /**
     * @return Collection|UserServices[]
     */
    public function getUserServices()
    {
        return $this->userServices;
    }

    public function addUserServices(UserServices $userService): self
    {
        if (!$this->userServices->contains($userService)) {
            $this->userServices[] = $userService;
            $userService->setIdUser($this);
        }

        return $this;
    }

    public function removeUserServices(UserServices $userService): self
    {
        if ($this->userServices->contains($userService)) {
            $this->userServices->removeElement($userService);
            // set the owning side to null (unless already changed)
            if ($userService->getIdUser() === $this) {
                $userService->setIdUser(null);
            }
        }

        return $this;
    }
}
