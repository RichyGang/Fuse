<?php

namespace App\Entity;

use App\Repository\RessourceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Cocur\Slugify\Slugify;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=RessourceRepository::class)
 */
class Ressource
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="ressources")
     */
    private $author;

    /**
     * @ORM\Column(type="datetime")
     */
    private $change_at;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class)
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank
     */
    private $category;

    /**
     * @ORM\ManyToMany(targetEntity=RessourceAttribute::class, inversedBy="ressources")
     */
    private $ressource_attribute;

    /**
     * @ORM\Column(type="string")
     */
    private $ressourcePicture;


    public function __construct()
    {
        $this->author = new ArrayCollection();
        $this->ressource_attribute = new ArrayCollection();
        $this->change_at = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|User[]
     */
    public function getAuthor(): Collection
    {
        return $this->author;
    }

    public function addAuthor(User $author): self
    {
        if (!$this->author->contains($author)) {
            $this->author[] = $author;
        }

        return $this;
    }

    public function removeAuthor(User $author): self
    {
        $this->author->removeElement($author);

        return $this;
    }

    public function getChangeAt(): ?\DateTimeInterface
    {
        return $this->change_at;
    }

    public function setChangeAt(\DateTimeInterface $change_at): self
    {
        $this->change_at = $change_at;

        return $this;
    }


    public function getSlug():string
    {
        return $slugify = (new Slugify())->slugify($this->id);
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|RessourceAttribute[]
     */
    public function getRessourceAttribute(): Collection
    {
        return $this->ressource_attribute;
    }

    public function addRessourceAttribute(RessourceAttribute $ressourceAttribute): self
    {
        if (!$this->ressource_attribute->contains($ressourceAttribute)) {
            $this->ressource_attribute[] = $ressourceAttribute;
        }

        return $this;
    }

    public function removeRessourceAttribute(RessourceAttribute $ressourceAttribute): self
    {
        $this->ressource_attribute->removeElement($ressourceAttribute);

        return $this;
    }

    public function getRessourcePicture()
    {
        return $this->ressourcePicture;
    }

    public function setRessourcePicture($ressourcePicture)
    {
        $this->ressourcePicture = $ressourcePicture;

        return $this;
    }

//    public function __toString() {
//        if(is_null($this->author)) {
//            return 'NULL';
//        }
//        return $this->author;
//    }
}
