<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CategorieRepository::class)
 */
class Categorie
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $ordre;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=Type::class, inversedBy="categories")
     * @ORM\JoinColumn(nullable=false)
     */
    private $type;

    /**
     * @ORM\OneToMany(targetEntity=Echantillon::class, mappedBy="categorie")
     */
    private $echantillons;

    /**
     * @ORM\ManyToMany(targetEntity=Profil::class, mappedBy="choix_degustation")
     */
    private $jures;

    /**
     * @ORM\OneToMany(targetEntity=Procede::class, mappedBy="categorie")
     */
    private $procedes;

    public function __construct()
    {
        $this->echantillons = new ArrayCollection();
        $this->jures = new ArrayCollection();
        $this->procedes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrdre(): ?int
    {
        return $this->ordre;
    }

    public function setOrdre(int $ordre): self
    {
        $this->ordre = $ordre;

        return $this;
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

    public function getType(): ?Type
    {
        return $this->type;
    }

    public function setType(?Type $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection|Echantillon[]
     */
    public function getEchantillons(): Collection
    {
        return $this->echantillons;
    }

    public function addEchantillon(Echantillon $echantillon): self
    {
        if (!$this->echantillons->contains($echantillon)) {
            $this->echantillons[] = $echantillon;
            $echantillon->setCategorie($this);
        }

        return $this;
    }

    public function removeEchantillon(Echantillon $echantillon): self
    {
        if ($this->echantillons->removeElement($echantillon)) {
            // set the owning side to null (unless already changed)
            if ($echantillon->getCategorie() === $this) {
                $echantillon->setCategorie(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Profil[]
     */
    public function getJures(): Collection
    {
        return $this->jures;
    }

    public function addJure(Profil $jure): self
    {
        if (!$this->jures->contains($jure)) {
            $this->jures[] = $jure;
            $jure->addChoixDegustation($this);
        }

        return $this;
    }

    public function removeJure(Profil $jure): self
    {
        if ($this->jures->removeElement($jure)) {
            $jure->removeChoixDegustation($this);
        }

        return $this;
    }

    /**
     * @return Collection|Procede[]
     */
    public function getProcedes(): Collection
    {
        return $this->procedes;
    }

    public function addProcede(Procede $procede): self
    {
        if (!$this->procedes->contains($procede)) {
            $this->procedes[] = $procede;
            $procede->setCategorie($this);
        }

        return $this;
    }

    public function removeProcede(Procede $procede): self
    {
        if ($this->procedes->removeElement($procede)) {
            // set the owning side to null (unless already changed)
            if ($procede->getCategorie() === $this) {
                $procede->setCategorie(null);
            }
        }

        return $this;
    }
}
