<?php

namespace App\Entity;

use App\Repository\TypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TypeRepository::class)
 */
class Type
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $ordre;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="integer")
     */
    private $vol_min_lot;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbre_max_ech;

    /**
     * @ORM\OneToMany(targetEntity=Categorie::class, mappedBy="type")
     */
    private $categories;

    /**
     * @ORM\ManyToOne(targetEntity=Concours::class, inversedBy="types")
     * @ORM\JoinColumn(nullable=false)
     */
    private $concours;

    /**
     * @ORM\Column(type="string", length=2)
     */
    private $unite;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrdre(): ?int
    {
        return $this->ordre;
    }

    public function setOrdre(?int $ordre): self
    {
        $this->ordre = $ordre;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getVolMinLot(): ?int
    {
        return $this->vol_min_lot;
    }

    public function setVolMinLot(int $vol_min_lot): self
    {
        $this->vol_min_lot = $vol_min_lot;

        return $this;
    }

    public function getNbreMaxEch(): ?int
    {
        return $this->nbre_max_ech;
    }

    public function setNbreMaxEch(int $nbre_max_ech): self
    {
        $this->nbre_max_ech = $nbre_max_ech;

        return $this;
    }

    /**
     * @return Collection|Categorie[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Categorie $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
            $category->setType($this);
        }

        return $this;
    }

    public function removeCategory(Categorie $category): self
    {
        if ($this->categories->removeElement($category)) {
            // set the owning side to null (unless already changed)
            if ($category->getType() === $this) {
                $category->setType(null);
            }
        }

        return $this;
    }

    public function getConcours(): ?Concours
    {
        return $this->concours;
    }

    public function setConcours(?Concours $concours): self
    {
        $this->concours = $concours;

        return $this;
    }

    public function getUnite(): ?string
    {
        return $this->unite;
    }

    public function setUnite(string $unite): self
    {
        $this->unite = $unite;

        return $this;
    }
}
