<?php

namespace App\Entity;

use App\Repository\CategoryAttributeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CategoryAttributeRepository::class)
 */
class CategoryAttribute
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $unity;

    /**
     * @ORM\ManyToMany(targetEntity=Category::class, inversedBy="categoryAttributes")
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity=RessourceAttribute::class, mappedBy="categoryAttribute")
     */
    private $ressourceAttributes;

    public function __construct()
    {
        $this->category = new ArrayCollection();
        $this->ressourceAttributes = new ArrayCollection();
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

    public function getUnity(): ?string
    {
        return $this->unity;
    }

    public function setUnity(?string $unity): self
    {
        $this->unity = $unity;

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategory(): Collection
    {
        return $this->category;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->category->contains($category)) {
            $this->category[] = $category;
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        $this->category->removeElement($category);

        return $this;
    }

    /**
     * @return Collection|RessourceAttribute[]
     */
    public function getRessourceAttributes(): Collection
    {
        return $this->ressourceAttributes;
    }

    public function addRessourceAttribute(RessourceAttribute $ressourceAttribute): self
    {
        if (!$this->ressourceAttributes->contains($ressourceAttribute)) {
            $this->ressourceAttributes[] = $ressourceAttribute;
            $ressourceAttribute->setCategoryAttribute($this);
        }

        return $this;
    }

    public function removeRessourceAttribute(RessourceAttribute $ressourceAttribute): self
    {
        if ($this->ressourceAttributes->removeElement($ressourceAttribute)) {
            // set the owning side to null (unless already changed)
            if ($ressourceAttribute->getCategoryAttribute() === $this) {
                $ressourceAttribute->setCategoryAttribute(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }

}
