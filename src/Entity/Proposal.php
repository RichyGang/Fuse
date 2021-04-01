<?php

namespace App\Entity;

use App\Repository\ProposalRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProposalRepository", repositoryClass=ProposalRepository::class)
 */
class Proposal
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Ressource::class, inversedBy="proposals")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank
     */
    private $ressource;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $quantity;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $unity;

    /**
     * @ORM\Column(type="text")
     */
    private $location;

    /**
     * @ORM\Column(type="date")
     */
    private $start;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $deadline;


    /**
     * @ORM\Column(type="boolean")
     */
    private $need_or_ask;

    /**
     * @ORM\Column(type="datetime")
     */
    private $create_at;

    public function __construct()
    {
        $this->create_at = new \DateTime();
        $this->start=new \DateTime();
        $this->deadline = new \DateTime();
    }

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $done = false;


    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="proposals")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRessource(): ?Ressource
    {
        return $this->ressource;
    }

    public function setRessource(?Ressource $ressource): self
    {
        $this->ressource = $ressource;

        return $this;
    }

    public function getQuantity(): ?string
    {
        return $this->quantity;
    }

    public function setQuantity(string $quantity): self
    {
        $this->quantity = $quantity;

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

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getDeadline(): ?\DateTimeInterface
    {
        return $this->deadline;
    }

    public function setDeadline(\DateTimeInterface $deadline): self
    {
        $this->deadline = $deadline;

        return $this;
    }


    public function getNeedOrAsk(): ?bool
    {
        return $this->need_or_ask;
    }

    public function setNeedOrAsk(bool $need_or_ask): self
    {
        $this->need_or_ask = $need_or_ask;

        return $this;
    }

    public function getCreateAt(): ?\DateTimeInterface
    {
        return $this->create_at;
    }

    public function setCreateAt(\DateTimeInterface $create_at): self
    {
        $this->create_at = $create_at;

        return $this;
    }

    public function getDone(): ?bool
    {
        return $this->done;
    }

    public function setDone(bool $done): self
    {
        $this->done = $done;

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
}
