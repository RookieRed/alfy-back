<?php

namespace App\Entity;

use App\Enum\UserRoles;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $userName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $salt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $birthDay;

    /**
     * @ORM\Column(type="json")
     * @var array
     */
    private $roles;

    /**
     * @var Baccalaureate
     * @ORM\ManyToOne(targetEntity="App\Entity\Baccalaureate")
     */
    private $baccalaureate;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\University", inversedBy="users")
     */
    private $universities;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="godfather")
     */
    private $godsons;

    /**
     * @var User|null
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="godsons")
     */
    private $godfather;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->universities = new ArrayCollection();
        $this->godsons = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUserName(): ?string
    {
        return $this->userName;
    }

    public function setUserName(string $userName): self
    {
        $this->userName = $userName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
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

    public function getSalt(): ?string
    {
        return $this->salt;
    }

    public function setSalt(string $salt): self
    {
        $this->salt = $salt;

        return $this;
    }

    public function getBirthDay(): ?\DateTimeInterface
    {
        return $this->birthDay;
    }

    public function setBirthDay(\DateTimeInterface $birthDay): self
    {
        $this->birthDay = $birthDay;

        return $this;
    }

    /**
     * @return array
     */
    public function getRoles(): ?array
    {
        return $this->roles;
    }

    /**
     * Check a role.
     * @param string $role
     * @return bool
     */
    public function isRole(string $role): bool
    {
        if ($this->roles->contains(UserRoles::ADMIN))
            return true;
        return $this->roles->contains($role);
    }

    /**
     * @param string $role
     * @return User
     */
    public function addRole(string $role): self
    {
        $this->roles->add($role);

        return $this;
    }

    /**
     * Removes a role.
     * @param string $role
     * @return User
     */
    public function removeRole(string $role): self
    {
        if ($this->isRole($role)) {
            $this->roles->remove(array_search($role, $this->roles->toArray()));
        }
        return $this;
    }

    /**
     * @return \App\Entity\Baccalaureate
     */
    public function getBaccalaureate(): ?Baccalaureate
    {
        return $this->baccalaureate;
    }

    /**
     * @param Baccalaureate $baccalaureate
     * @return User
     */
    public function setBaccalaureate(Baccalaureate $baccalaureate): self
    {
        $this->baccalaureate = $baccalaureate;

        return $this;
    }

    /**
     * @return Collection|University[]
     */
    public function getUniversities(): Collection
    {
        return $this->universities;
    }

    public function addUniversity(University $university): self
    {
        if (!$this->universities->contains($university)) {
            $this->universities[] = $university;
        }

        return $this;
    }

    public function removeUniversity(University $university): self
    {
        if ($this->universities->contains($university)) {
            $this->universities->removeElement($university);
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getGodsons(): Collection
    {
        return $this->godsons;
    }

    public function addGodson(User $godson): self
    {
        if (!$this->godsons->contains($godson)) {
            $this->godsons[] = $godson;
            $godson->setGodfather($this);
        }

        return $this;
    }

    public function removeGodson(User $godson): self
    {
        if ($this->godsons->contains($godson)) {
            $this->godsons->removeElement($godson);
            // set the owning side to null (unless already changed)
            if ($godson->getGodsons() === $this) {
                $godson->setGodfather(null);
            }
        }

        return $this;
    }

    /**
     * @param User|null $godfather
     * @return User
     */
    public function setGodfather(?User $godfather): self
    {
        $this->godfather = $godfather;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getGodfather(): ?User
    {
        return $this->godfather;
    }
}
