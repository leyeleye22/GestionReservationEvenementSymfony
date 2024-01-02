<?php

namespace App\Entity;

use App\Repository\FormationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FormationRepository::class)]
class Formation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]

    /**
     * @Assert\NotBlank
     * @Assert\Length(min=3, max=255)
     */
    private ?string $nomFormation = null;

    #[ORM\Column(length: 255)]

    /**
     * @Assert\NotBlank
     * @Assert\Length(min=3, max=255)
     */
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]

    
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(length: 255)]
    private ?string $dureFormation = null;

    #[ORM\Column(length: 255)]

    /**
     * @Assert\NotBlank
     * @Assert\Length(min=3, max=255)
     */
    private ?string $niveau = null;

    #[ORM\Column]
    /**
     * @Assert\NotBlank
     * 
     */
    private ?int $placeDisponible = null;

    #[ORM\Column(length: 255)]

/**
     * @Assert\NotBlank
     * 
     */    private ?string $domaineFormation = null;

    #[ORM\OneToMany(mappedBy: 'formation', targetEntity: Candidature::class)]
    private Collection $candidatures;

    
   

    public function __construct()
    {
        $this->candidatures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomFormation(): ?string
    {
        return $this->nomFormation;
    }

    public function setNomFormation(string $nomFormation): static
    {
        $this->nomFormation = $nomFormation;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): static
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    public function getDureFormation(): ?string
    {
        return $this->dureFormation;
    }

    public function setDureFormation(string $dureFormation): static
    {
        $this->dureFormation = $dureFormation;

        return $this;
    }

    public function getNiveau(): ?string
    {
        return $this->niveau;
    }

    public function setNiveau(string $niveau): static
    {
        $this->niveau = $niveau;

        return $this;
    }

    public function getPlaceDisponible(): ?int
    {
        return $this->placeDisponible;
    }

    public function setPlaceDisponible(int $placeDisponible): static
    {
        $this->placeDisponible = $placeDisponible;

        return $this;
    }

    public function getDomaineFormation(): ?string
    {
        return $this->domaineFormation;
    }

    public function setDomaineFormation(string $domaineFormation): static
    {
        $this->domaineFormation = $domaineFormation;

        return $this;
    }

    /**
     * @return Collection<int, Candidature>
     */
    public function getCandidatures(): Collection
    {
        return $this->candidatures;
    }

    public function addCandidature(Candidature $candidature): static
    {
        if (!$this->candidatures->contains($candidature)) {
            $this->candidatures->add($candidature);
            $candidature->setFormation($this);
        }

        return $this;
    }

    public function removeCandidature(Candidature $candidature): static
    {
        if ($this->candidatures->removeElement($candidature)) {
            // set the owning side to null (unless already changed)
            if ($candidature->getFormation() === $this) {
                $candidature->setFormation(null);
            }
        }

        return $this;
    }
}
