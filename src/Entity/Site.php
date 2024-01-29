<?php

namespace App\Entity;

use App\Repository\SiteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SiteRepository::class)]
class Site
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $nom = null;

    #[ORM\OneToMany(mappedBy: 'site', targetEntity: Participant::class)]
    private Collection $partipants;

    public function __construct()
    {
        $this->partipants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection<int, Participant>
     */
    public function getPartipants(): Collection
    {
        return $this->partipants;
    }

    public function addPartipant(Participant $partipant): static
    {
        if (!$this->partipants->contains($partipant)) {
            $this->partipants->add($partipant);
            $partipant->setSite($this);
        }

        return $this;
    }

    public function removePartipant(Participant $partipant): static
    {
        if ($this->partipants->removeElement($partipant)) {
            // set the owning side to null (unless already changed)
            if ($partipant->getSite() === $this) {
                $partipant->setSite(null);
            }
        }

        return $this;
    }
}
