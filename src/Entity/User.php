<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="user")
 * @ORM\Entity
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=64)
     */
    protected $token;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    protected $email;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    /**
     * @var bool
     *
     * @ORM\Column(name="role", type="boolean")
     */
    private $role;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $password;

    /**
     * 
     * @Assert\Length(max=4096)
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="string", length=24, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name_company;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $street;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $code_post;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=24)
     */
    private $nip;

    /**
     * @ORM\Column(type="string", length=24)
     */
    private $regon;

    /**
     * @ORM\Column(type="string", length=24)
     */
    private $ip;

    /**
     * @ORM\Column(type="string", length=24, nullable=true)
     */
    private $code_partner;

    private $akcept;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserNotify", mappedBy="user")
     */
    private $userNotifies;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserServices", mappedBy="user")
     */
    private $userServices;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserInvoices", mappedBy="user")
     */
    private $userInvoices;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Partners", mappedBy="user")
     */
    private $partner;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\UserDiscounts", mappedBy="user")
     */
    private $discount;


    public function __toString()
    {
        return (string) $this->firstname.' '.$this->name;
    }

    public function __construct()
    {
        $this->active = false;
        $this->isActive = true;
        $this->akcept = false;
        $this->userNotifies = new ArrayCollection();
        $this->userServices = new ArrayCollection();
        $this->userInvoices = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setToken() {
        $time = time();
        $token = $time.''.$this->email;
        $token = md5($token);
        $token = substr($token, 1, 25);

        $this->token = $token;
    }

    public function getToken()
    {
        return $this->token;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->firstname.' '.$this->name;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set active.
     *
     * @param bool $active
     *
     * @return User
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active.
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->active;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }

    public function newPassword() 
    {
        // generowanie nowego hasła
        $number = round('100, 9999');
        $time = time();
        $pass = $time.''.$number;
        $pass = md5($pass);
        $pass = substr($pass, 1, 10);

        return $pass;
    }

    public function getSalt()
    {
        // The bcrypt and argon2i algorithms don't require a separate salt.
        // You *may* need a real salt if you choose a different encoder.
        return null;
    }

    public function getRoles()
    {
        if($this->role == true) {
            return array('ROLE_USER', 'ROLE_ADMIN');
        } else {
            return array('ROLE_USER');
        }
    }

    public function setRole($role)
    {
        $this->role = $role;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function eraseCredentials()
    {
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function getNameCompany(): ?string
    {
        return $this->name_company;
    }

    public function setNameCompany(string $name): self
    {
        $this->name_company = $name;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $name): self
    {
        $this->firstname = $name;

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

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getCodePost(): ?string
    {
        return $this->code_post;
    }

    public function setCodePost(string $code_post): self
    {
        $this->code_post = $code_post;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getNip(): ?string
    {
        return $this->nip;
    }

    public function setNip(string $nip): self
    {
        $this->nip = $nip;

        return $this;
    }

    public function getRegon(): ?string
    {
        return $this->regon;
    }

    public function setRegon(string $regon): self
    {
        $this->regon = $regon;

        return $this;
    }

    public function getCodePartner(): ?string
    {
        return $this->code_partner;
    }

    public function setCodePartner(string $code)
    {
        $this->code_partner = $code;

        return $this;
    }

    public function getIp() {
        return $this->ip;
    }

    public function setIp($ip) {
        $this->ip = $ip;

        return $this;
    }

    public function getAkcept() 
    {
        return $this->akcept;
    }

    public function setAkcept($akcept) 
    {
        return $this->akcept = $akcept;
    }

    /**
     * @return Collection|UserNotify[]
     */
    public function getUserNotifies(): Collection
    {
        return $this->userNotifies;
    }

    public function addUserNotify(UserNotify $userNotify): self
    {
        if (!$this->userNotifies->contains($userNotify)) {
            $this->userNotifies[] = $userNotify;
            $userNotify->setIdUser($this);
        }

        return $this;
    }

    public function removeUserNotify(UserNotify $userNotify): self
    {
        if ($this->userNotifies->contains($userNotify)) {
            $this->userNotifies->removeElement($userNotify);
            // set the owning side to null (unless already changed)
            if ($userNotify->getIdUser() === $this) {
                $userNotify->setIdUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UserServices[]
     */
    public function getUserServices(): Collection
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

    /**
     * @return Collection|UserInvoices[]
     */
    public function getUserInvoices(): Collection
    {
        return $this->userInvoices;
    }

    public function addUserInvoices(UserInvoices $userInvoice): self
    {
        if (!$this->userInvoices->contains($userInvoice)) {
            $this->userInvoices[] = $userInvoice;
            $userInvoice->setIdUser($this);
        }

        return $this;
    }

    public function removeUserInvoices(UserInvoices $userInvoice): self
    {
        if ($this->userInvoices->contains($userInvoice)) {
            $this->userInvoices->removeElement($userInvoice);
            // set the owning side to null (unless already changed)
            if ($userInvoice->getIdUser() === $this) {
                $userInvoice->setIdUser(null);
            }
        }

        return $this;
    }

}
