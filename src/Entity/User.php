<?php

namespace App\Entity;

use App\Constants\SocialNetworkUrl;
use App\Constants\UserRoles;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"user_get", "user_get_list"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     * @Groups({"account_create", "user_connect", "user_get", "user_update", "user_get_list"})
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Groups({"account_create", "user_get", "user_update", "user_get_list"})
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Groups({"account_create", "user_get", "user_update", "user_get_list"})
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"account_create", "user_connect", "user_update"})
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $salt;

    /**
     * @var string
     */
    private $clearPassword;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Groups({"account_create", "user_get", "user_update"})
     */
    private $birthDay;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, unique=true, nullable=true)
     * @Assert\Email()
     * @Groups({"account_create", "user_get", "user_update"})
     */
    private $email;

    /**
     * @var string
     * @ORM\Column(type="string", length=20, nullable=true)
     * @Groups({"account_create", "user_get", "user_update"})
     */
    private $phone;

    /**
     * @var Address|null
     * @ORM\OneToOne(targetEntity="App\Entity\Address", fetch="LAZY")
     * @ORM\JoinColumn(name="address_id", referencedColumnName="id")
     * @Groups({"user_get", "user_update"})
     * @MaxDepth(2)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank()
     * @var string
     * @Groups({"user_get"})
     */
    private $role;

    /**
     * @var File|null
     * @ORM\OneToOne(targetEntity="App\Entity\File")
     * @ORM\JoinColumn(name="picture_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     * @Groups({"user_get", "user_get_list"})
     */
    private $profilePicture;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=150, nullable=true)
     * @Groups({"user_get", "user_update"})
     */
    private $facebook;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=150, nullable=true)
     * @Groups({"user_get", "user_update"})
     */
    private $linkedIn;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=150, nullable=true)
     * @Groups({"user_get", "user_update"})
     */
    private $twitter;

    /**
     * @var Baccalaureate
     * @ORM\ManyToOne(targetEntity="App\Entity\Baccalaureate", fetch="EAGER")
     */
    private $baccalaureate;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\University", inversedBy="users")
     */
    private $universities;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\User", mappedBy="sponsor")
     */
    private $sponsoredUsers;

    /**
     * @var User|null
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="sponsoredUsers")
     */
    private $sponsor;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PageContent", mappedBy="creator")
     */
    private $pageContents;

    public function __construct()
    {
        $this->universities = new ArrayCollection();
        $this->sponsoredUsers = new ArrayCollection();
        $this->pageContents = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return implode( ' ',
                array_map(function ($val) {
                    return ucfirst($val);
                }, preg_split('/(\s|-|_)/', $this->firstName))
        );
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return strtoupper($this->lastName);
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getBirthDay(): ?\DateTimeInterface
    {
        return $this->birthDay;
    }

    public function setBirthDay($birthDay): self
    {
        $this->birthDay = $birthDay;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return User
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     * @return User
     */
    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getRole(): ?string
    {
        return $this->role;
    }


    /**
     * Check a role.
     * @param string $role
     * @return bool
     */
    public function isRole(?string $role): bool
    {
        if ($this->role == UserRoles::ADMIN)
            return true;

        return $this->role == $role;
    }

    /**
     * @param string $role
     */
    public function setRole(?string $role): self
    {
        $this->role = $role;

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
    public function setBaccalaureate(?Baccalaureate $baccalaureate): self
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
    public function getSponsoredUsers(): Collection
    {
        return $this->sponsoredUsers;
    }

    public function addSponsoredUser(User $sponsored): self
    {
        if (!$this->sponsoredUsers->contains($sponsored)) {
            $this->sponsoredUsers[] = $sponsored;
            $sponsored->setSponsor($this);
        }

        return $this;
    }

    public function removeSponsoredUser(User $sponsored): self
    {
        if ($this->sponsoredUsers->contains($sponsored)) {
            $this->sponsoredUsers->removeElement($sponsored);
            // set the owning side to null (unless already changed)
            if ($sponsored->getSponsoredUsers() === $this) {
                $sponsored->setSponsor(null);
            }
        }

        return $this;
    }

    /**
     * @param User|null $sponsor
     * @return User
     */
    public function setSponsor(?User $sponsor): self
    {
        $this->sponsor = $sponsor;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getSponsor(): ?User
    {
        return $this->sponsor;
    }

    /**
     * @return string
     */
    public function getClearPassword(): ?string
    {
        return $this->clearPassword;
    }

    /**
     * @param string $clearPassword
     * @return User
     */
    public function setClearPassword(?string $clearPassword): self
    {
        $this->clearPassword = $clearPassword;

        return $this;
    }

    /**
     * @return Address|null
     */
    public function getAddress(): ?Address
    {
        return $this->address;
    }

    /**
     * @param Address|null $address
     * @return User
     */
    public function setAddress($address): self
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @return File|null
     */
    public function getProfilePicture(): ?File
    {
        return $this->profilePicture;
    }

    /**
     * @param File|null $profilePicture
     * @return User
     */
    public function setProfilePicture(?File $profilePicture): self
    {
        $this->profilePicture = $profilePicture;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getFacebook(): ?string
    {
        if ($this->facebook != null) {
            if (strstr($this->facebook, SocialNetworkUrl::FACEBOOK)) {
                return $this->facebook;
            }
            return SocialNetworkUrl::FACEBOOK . $this->facebook;
        }
        return null;
    }

    /**
     * @param null|string $facebook
     * @return User
     */
    public function setFacebook(?string $facebook): self
    {
        $path = parse_url($facebook, PHP_URL_PATH);
        $this->facebook = strlen($path) > 1 ? $path : null;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getLinkedIn(): ?string
    {
        if ($this->linkedIn != null) {
            if (strstr($this->linkedIn, SocialNetworkUrl::LINKEDIN)) {
                return $this->linkedIn;
            }
            return SocialNetworkUrl::LINKEDIN . $this->linkedIn;
        }
        return null;
    }

    /**
     * @param null|string $linkedIn
     * @return User
     */
    public function setLinkedIn(?string $linkedIn): self
    {
        $path = parse_url($linkedIn, PHP_URL_PATH);
        $this->linkedIn = strlen($path) > 1 ? $path : null;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getTwitter(): ?string
    {
        if ($this->twitter != null) {
            if (strstr($this->twitter, SocialNetworkUrl::TWITTER)) {
                return $this->twitter;
            }
            return SocialNetworkUrl::TWITTER . $this->twitter;
        }
        return null;
    }

    /**
     * @param null|string $twitter
     * @return User
     */
    public function setTwitter(?string $twitter): self
    {
        $path = parse_url($twitter, PHP_URL_PATH);
        $this->twitter = strlen($path) > 1 ? $path : null;
        return $this;
    }


    // =====================================================
    //                  USER INTERFACE METHODS
    // =====================================================

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        $this->clearPassword = null;
    }

    public function getRoles()
    {
        return [$this->role];
    }

    public function getSalt(): ?string
    {
        return $this->salt;
    }

    public function setSalt(?string $salt): self
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * @return Collection|PageContent[]
     */
    public function getPageContents(): Collection
    {
        return $this->pageContents;
    }

    public function addPageContent(PageContent $pageContent): self
    {
        if (!$this->pageContents->contains($pageContent)) {
            $this->pageContents[] = $pageContent;
            $pageContent->setCreator($this);
        }

        return $this;
    }

    public function removePageContent(PageContent $pageContent): self
    {
        if ($this->pageContents->contains($pageContent)) {
            $this->pageContents->removeElement($pageContent);
            // set the owning side to null (unless already changed)
            if ($pageContent->getCreator() === $this) {
                $pageContent->setCreator(null);
            }
        }

        return $this;
    }
}
