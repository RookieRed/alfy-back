<?php

namespace App\Entity;

use App\Entity\Traits\TimedEntityTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FileRepository")
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(
 *      name="file_unicity_path_name", columns={"path", "name"})})
 */
class File
{
    use TimedEntityTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user_get", "user_get_list", "get_page"})
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user_get", "user_get_list", "get_page"})
     * @Assert\NotBlank()
     * @Assert\Regex("/^\/((.+)\/)?$/")
     */
    private $path;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=true, fieldName="owner_id", onDelete="CASCADE")
     */
    private $owner;

    /**
     * @ORM\Column(type="json", nullable=true)
     * @Groups({"get_page"})
     */
    private $config = [];

    /**
     * @ORM\ManyToMany(targetEntity="SlideShowSection", mappedBy="photos")
     */
    private $relatedSlides;

    public function __construct()
    {
        $this->relatedSlides = new ArrayCollection();
        $this->setCreatedAt(null);
    }

    public function getId()
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

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @Groups({"user_get", "user_get_list", "get_page"})
     */
    public function getFullPath(): string
    {
        return $this->path . $this->name;
    }

    /**
     * @return User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param mixed $owner
     * @return File
     */
    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }


    public function getConfig(): ?array
    {
        return $this->config;
    }

    public function setConfig(?array $config): self
    {
        $this->config = $config;

        return $this;
    }

    /**
     * @return Collection|SlideShowSection[]
     */
    public function getRelatedSlides(): Collection
    {
        return $this->relatedSlides;
    }

    public function addRelatedDiapo(SlideShowSection $relatedDiapo): self
    {
        if (!$this->relatedSlides->contains($relatedDiapo)) {
            $this->relatedSlides[] = $relatedDiapo;
            $relatedDiapo->addPhoto($this);
        }

        return $this;
    }

    public function removeRelatedDiapo(SlideShowSection $relatedDiapo): self
    {
        if ($this->relatedSlides->contains($relatedDiapo)) {
            $this->relatedSlides->removeElement($relatedDiapo);
            $relatedDiapo->removePhoto($this);
        }

        return $this;
    }

    /**
     * @ORM\PreRemove()
     */
    public function onPreRemoveEntity()
    {
        $filePath = '..'. DIRECTORY_SEPARATOR .'..'. DIRECTORY_SEPARATOR .'public' . $this->getFullPath();
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    /**
     * @ORM\PreUpdate()
     * @ORM\PrePersist()
     */
    public function findPhysicalFileOrThrowException()
    {
        $filePath = '..'. DIRECTORY_SEPARATOR .'..'. DIRECTORY_SEPARATOR .'public' . $this->getFullPath();
        if (!file_exists($filePath)) {
            throw new FileNotFoundException("File entity is linked to a non-existing file.");
        }
    }
}
