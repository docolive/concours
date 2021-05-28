<?php

namespace App\Entity;

use App\Repository\TableRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TableRepository::class)
 * @ORM\Table(name="`table`")
 */
class Table
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
     * @ORM\Column(type="integer")
     */
    private $maxEchs;

    /**
     * @ORM\ManyToOne(targetEntity=Categorie::class, inversedBy="tables")
     */
    private $categorie;

    /**
     * @ORM\OneToMany(targetEntity=Echantillon::class, mappedBy="tableJury")
     */
    private $echantillons;

    public function __construct()
    {
        $this->echantillons = new ArrayCollection();
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

    public function getMaxEchs(): ?int
    {
        return $this->maxEchs;
    }

    public function setMaxEchs(int $maxEchs): self
    {
        $this->maxEchs = $maxEchs;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;

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
            $echantillon->setTableJury($this);
        }

        return $this;
    }

    public function removeEchantillon(Echantillon $echantillon): self
    {
        if ($this->echantillons->removeElement($echantillon)) {
            // set the owning side to null (unless already changed)
            if ($echantillon->getTableJury() === $this) {
                $echantillon->setTableJury(null);
            }
        }

        return $this;
    }
}
