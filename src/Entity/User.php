<?php

namespace App\Entity;

use App\Constants\SocialNetworkUrl;
use App\Constants\UserRoles;
use App\Entity\Traits\AddressedEntityTrait;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    use AddressedEntityTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"user_get", "user_get_list", "user_get_info"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank(groups={"account_create", "user_update"})
     * @Groups({"account_create", "user_connect", "user_get", "user_update", "user_get_list", "get_page", "user_get_info"})
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(groups={"account_create", "user_update"})
     * @Groups({"account_create", "user_get", "user_update", "user_get_list", "get_page", "user_get_info"})
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(groups={"account_create", "user_update"})
     * @Groups({"account_create", "user_get", "user_update", "user_get_list", "get_page", "user_get_info"})
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(min="8", minMessage="Password must be at least 8 characters long.", groups={"user_update", "password_update"})
     * @Assert\NotCompromisedPassword(groups={"user_update", "password_update"})
     * @Assert\Regex(message="Password must contain at least one lower case letter, one upper case letter and one digit.",
     *     pattern="/^(.*[0-9].*[a-z].*[A-Z].*)|(.*[0-9].*[A-Z].*[a-z].*)|(.*[a-z].*[0-9].*[A-Z].*)|(.*[a-z].*[A-Z].*[0-9].*)|(.*[A-Z].*[a-z].*[0-9].*)|(.*[A-Z].*[0-9].*[a-z].*)$/",
     *     groups={"user_update", "account_create", "password_update"}, )
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
     * @Assert\Date(groups={"account_create", "user_update"})
     * @Assert\LessThan(value="today", message="Your birthday date can not be in the future.", groups={"account_create", "user_update"})
     * @Groups({"account_create", "user_get", "user_update"})
     */
    private $birthDay;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, unique=true, nullable=true)
     * @Assert\NotNull(groups={"account_create"})
     * @Assert\Email(groups={"account_create"})
     * @Groups({"account_create", "user_get", "user_get_info"})
     */
    private $email;

    /**
     * @var string
     * @ORM\Column(type="string", length=20, nullable=true)
     * @Assert\Regex(pattern="/^\+?(\d+|\ )+$/", message="Phone number can contain a + digits and spaces.",
     *     groups={"account_create", "user_update"})
     * @Groups({"account_create", "user_get", "user_update"})
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(groups={"account_create", "user_update"})
     * @var string
     * @Groups({"user_get", "user_get_info"})
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
     * @var File|null
     * @ORM\OneToOne(targetEntity="App\Entity\File")
     * @ORM\JoinColumn(name="cover_picture_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     * @Groups({"user_get"})
     */
    private $coverPicture;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=150, nullable=true)
     * @Assert\Url(groups={"user_update"})
     * @Groups({"user_get", "user_update"})
     */
    private $facebook;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=150, nullable=true)
     * @Assert\Url(groups={"user_update"})
     * @Groups({"user_get", "user_update"})
     */
    private $linkedIn;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=150, nullable=true)
     * @Assert\Url(groups={"user_update"})
     * @Groups({"user_get", "user_update"})
     */
    private $twitter;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=150, nullable=true)
     * @Assert\Url(groups={"user_update"})
     * @Groups({"user_get", "user_update"})
     */
    private $instagram;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=200, nullable=true)
     * @Assert\Url(groups={"user_update"})
     * @Groups({"user_get", "user_update"})
     */
    private $personalWebsite;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Groups({"user_get", "user_update"})
     */
    private $baccalaureate;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Study", mappedBy="student", fetch="EAGER", cascade={"persist", "remove"}, orphanRemoval=true)
     * @Groups({"user_get"})
     */
    private $studies;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     * @Assert\NotBlank()
     * @Groups({"user_get", "user_update"})
     */
    private $jobTitle;

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
     * @ORM\OneToMany(targetEntity="App\Entity\Project", mappedBy="owner", orphanRemoval=true, cascade={"persist", "remove"})
     * @Groups({"user_get"})
     */
    private $projects;

    public function __construct()
    {
        $this->sponsoredUsers = new ArrayCollection();
        $this->studies = new ArrayCollection();
        $this->projects = new ArrayCollection();
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
        return implode(' ',
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

    public function getBirthDay(): ?DateTimeInterface
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
        $this->phone = trim($phone);

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

    public function getBaccalaureate(): ?string
    {
        return $this->baccalaureate;
    }

    /**
     * @return User
     */
    public function setBaccalaureate(?string $baccalaureate): self
    {
        $this->baccalaureate = $baccalaureate;

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

    /**
     * @return string|null
     */
    public function getPersonalWebsite(): ?string
    {
        return $this->personalWebsite;
    }

    public function setPersonalWebsite(?string $personalWebsite): self
    {
        $this->personalWebsite = $personalWebsite;
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

    public function getJobTitle(): ?string
    {
        return $this->jobTitle;
    }

    public function setJobTitle(?string $jobTitle): self
    {
        $this->jobTitle = $jobTitle;
        return $this;
    }

    public function getCoverPicture(): ?File
    {
        return $this->coverPicture;
    }

    public function setCoverPicture(?File $coverPicture): self
    {
        $this->coverPicture = $coverPicture;
        return $this;
    }

    public function getStudies(): Collection
    {
        return $this->studies;
    }


    public function addStudy(Study $study): self
    {
        if (!$this->studies->contains($study)) {
            $this->studies[] = $study;
            $study->setStudent($this);
        }

        return $this;
    }

    public function removeStudy(Study $study): self
    {
        if ($this->studies->contains($study)) {
            $this->studies->removeElement($study);
            // set the owning side to null (unless already changed)
            if ($study->getStudent() === $this) {
                $study->setStudent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Project[]
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): self
    {
        if (!$this->projects->contains($project)) {
            $this->projects[] = $project;
            $project->setOwner($this);
        }

        return $this;
    }

    public function removeProject(Project $project): self
    {
        if ($this->projects->contains($project)) {
            $this->projects->removeElement($project);
            // set the owning side to null (unless already changed)
            if ($project->getOwner() === $this) {
                $project->setOwner(null);
            }
        }

        return $this;
    }

    public function getInstagram(): ?string
    {
        return $this->instagram;
    }

    public function setInstagram(?string $instagram): self
    {
        $this->instagram = $instagram;
        return $this;
    }
}
