<?php

namespace App\Entity;

use App\Entity\Livraison;
use App\Validator as MyAssert;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\EchantillonRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=EchantillonRepository::class)
 * @MyAssert\CheckVariety(groups={"add","edit"})
 * @MyAssert\CheckVolume(groups={"add","edit"})
 * @MyAssert\CheckNbreEch(groups={"add"})
 * @MyAssert\CheckUniqueLot(groups={"add"})
 * @MyAssert\CheckUniqueLotEdit(groups={"edit"})
 * @MyAssert\CheckProcede(groups={"add","edit"})
 */
class Echantillon
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Categorie::class, inversedBy="echantillons")
     * @ORM\JoinColumn(nullable=false)
     */
    private $categorie;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lot;

    /**
     * @ORM\Column(type="integer")
     * 
     */
    private $volume;
    

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $public_ref;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $code;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="echantillons")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $variety;

    /**
     * @ORM\ManyToOne(targetEntity=Paiement::class, inversedBy="echantillons")
     */
    private $paiement;

    /**
     * @ORM\Column(type="boolean")
     */
    private $paye = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $recu = false;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $observation;

    /**
     * @ORM\ManyToOne(targetEntity=Medaille::class, inversedBy="echantillons")
     */
    private $medaille;

    /**
     * @ORM\ManyToOne(targetEntity=Livraison::class, inversedBy="echantillons")
     */
    private $livraison;

    /**
     * @ORM\ManyToOne(targetEntity=Procede::class, inversedBy="echantillons")
     * @ORM\JoinColumn(nullable=true)
     * 
     */
    private $procede;

    /**
     * @ORM\ManyToOne(targetEntity=Table::class, inversedBy="echantillons")
     */
    private $tableJury;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getLot(): ?string
    {
        return $this->lot;
    }

    public function setLot(string $lot): self
    {
        $this->lot = $lot;

        return $this;
    }

    public function getVolume(): ?int
    {
        return $this->volume;
    }

    public function setVolume(int $volume): self
    {
        $this->volume = $volume;

        return $this;
    }

    public function getPublicRef(): ?string
    {
        return $this->public_ref;
    }

    public function setPublicRef(string $public_ref): self
    {
        $this->public_ref = $public_ref;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

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

    public function getVariety(): ?string
    {
        return $this->variety;
    }

    public function setVariety(?string $variety): self
    {
        $this->variety = $variety;

        return $this;
    }

    public function getPaiement(): ?Paiement
    {
        return $this->paiement;
    }

    public function setPaiement(?Paiement $paiement): self
    {
        $this->paiement = $paiement;

        return $this;
    }

    public function getPaye(): ?bool
    {
        return $this->paye;
    }

    public function setPaye(bool $paye): self
    {
        $this->paye = $paye;

        return $this;
    }

    public function getRecu(): ?bool
    {
        return $this->recu;
    }

    public function setRecu(bool $recu): self
    {
        $this->recu = $recu;

        return $this;
    }

    public function getObservation(): ?string
    {
        return $this->observation;
    }

    public function setObservation(?string $observation): self
    {
        $this->observation = $observation;

        return $this;
    }

    public function getMedaille(): ?Medaille
    {
        return $this->medaille;
    }

    public function setMedaille(?Medaille $medaille): self
    {
        $this->medaille = $medaille;

        return $this;
    }

    public function getLivraison(): ?Livraison
    {
        return $this->livraison;
    }

    public function setLivraison(?Livraison $livraison): self
    {
        $this->livraison = $livraison;

        return $this;
    }

    public function getProcede(): ?Procede
    {
        return $this->procede;
    }

    public function setProcede(?Procede $procede): self
    {
        $this->procede = $procede;

        return $this;
    }

    public function getTableJury(): ?Table
    {
        return $this->tableJury;
    }

    public function setTableJury(?Table $tableJury): self
    {
        $this->tableJury = $tableJury;

        return $this;
    }
}
