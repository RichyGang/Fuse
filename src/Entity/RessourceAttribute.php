<?php

namespace App\Entity;

use App\Repository\RessourceAttributeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RessourceAttributeRepository::class)
 */
class RessourceAttribute
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
    private $value;

    /**
     * @ORM\ManyToOne(targetEntity=CategoryAttribute::class, inversedBy="ressourceAttributes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $categoryAttribute;


    /**
     * @ORM\ManyToMany(targetEntity=Ressource::class, mappedBy="attributes")
     */
    private $ressources;



    public function __construct()
    {
        $this->ressourceAttribute = new ArrayCollection();
        $this->ressources = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getCategoryAttribute(): ?CategoryAttribute
    {
        return $this->categoryAttribute;
    }

    public function setCategoryAttribute(?CategoryAttribute $categoryAttribute): self
    {
        $this->categoryAttribute = $categoryAttribute;

        return $this;
    }

    /**
     * @return Collection|Ressource[]
     */
    public function getRessourceAttribute(): Collection
    {
        return $this->ressourceAttribute;
    }

    public function addRessourceAttribute(Ressource $ressourceAttribute): self
    {
        if (!$this->ressourceAttribute->contains($ressourceAttribute)) {
            $this->ressourceAttribute[] = $ressourceAttribute;
        }

        return $this;
    }

    public function removeRessourceAttribute(Ressource $ressourceAttribute): self
    {
        $this->ressourceAttribute->removeElement($ressourceAttribute);

        return $this;
    }

    /**
     * @return Collection|Ressource[]
     */
    public function getRessources(): Collection
    {
        return $this->ressources;
    }

    public function addRessources(Ressource $ressources): self
    {
        if (!$this->ressources->contains($ressources)) {
            $this->ressources[] = $ressources;
            $ressources->addAttribute($this);
        }

        return $this;
    }

    public function removeRessources(Ressource $ressources): self
    {
        if ($this->ressources->removeElement($ressources)) {
            $ressources->removeAttribute($this);
        }

        return $this;
    }

    public function __toString() {
        if(is_null($this->value)) {
            return 'NULL';
        }
        return $this->value;
    }

}
