<?php

namespace App\Entity;

use App\Repository\ConcoursRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ConcoursRepository::class)
 */
class Concours
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
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lieu;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $debut_inscription;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fin_inscription;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2, nullable=true)
     */
    private $cout;

    /**
     * @ORM\Column(type="decimal", precision=4, scale=2, nullable=true)
     */
    private $tva;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $resp_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $resp_adress1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $resp_adress2;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $resp_adress3;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $resp_phone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $resp_email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $couv_palmares;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $adress1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $adress2;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $adress3;

    /**
     * @ORM\OneToMany(targetEntity=Type::class, mappedBy="concours")
     */
    private $types;

    /**
     * @ORM\OneToMany(targetEntity=Categorie::class, mappedBy="concours")
     */
    private $categorie;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->types = new ArrayCollection();
        $this->categorie = new ArrayCollection();
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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(?string $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getDebutInscription(): ?\DateTimeInterface
    {
        return $this->debut_inscription;
    }

    public function setDebutInscription(?\DateTimeInterface $debut_inscription): self
    {
        $this->debut_inscription = $debut_inscription;

        return $this;
    }

    public function getFinInscription(): ?\DateTimeInterface
    {
        return $this->fin_inscription;
    }

    public function setFinInscription(?\DateTimeInterface $fin_inscription): self
    {
        $this->fin_inscription = $fin_inscription;

        return $this;
    }

    public function getCout(): ?string
    {
        return $this->cout;
    }

    public function setCout(?string $cout): self
    {
        $this->cout = $cout;

        return $this;
    }

    public function getTva(): ?string
    {
        return $this->tva;
    }

    public function setTva(?string $tva): self
    {
        $this->tva = $tva;

        return $this;
    }

    public function getRespName(): ?string
    {
        return $this->resp_name;
    }

    public function setRespName(?string $resp_name): self
    {
        $this->resp_name = $resp_name;

        return $this;
    }

    public function getRespAdress1(): ?string
    {
        return $this->resp_adress1;
    }

    public function setRespAdress1(?string $resp_adress1): self
    {
        $this->resp_adress1 = $resp_adress1;

        return $this;
    }

    public function getRespAdress2(): ?string
    {
        return $this->resp_adress2;
    }

    public function setRespAdress2(?string $resp_adress2): self
    {
        $this->resp_adress2 = $resp_adress2;

        return $this;
    }

    public function getRespAdress3(): ?string
    {
        return $this->resp_adress3;
    }

    public function setRespAdress3(?string $resp_adress3): self
    {
        $this->resp_adress3 = $resp_adress3;

        return $this;
    }

    public function getRespPhone(): ?string
    {
        return $this->resp_phone;
    }

    public function setRespPhone(?string $resp_phone): self
    {
        $this->resp_phone = $resp_phone;

        return $this;
    }

    public function getRespEmail(): ?string
    {
        return $this->resp_email;
    }

    public function setRespEmail(?string $resp_email): self
    {
        $this->resp_email = $resp_email;

        return $this;
    }

    public function getCouvPalmares(): ?string
    {
        return $this->couv_palmares;
    }

    public function setCouvPalmares(?string $couv_palmares): self
    {
        $this->couv_palmares = $couv_palmares;

        return $this;
    }

    public function getAdress1(): ?string
    {
        return $this->adress1;
    }

    public function setAdress1(?string $adress1): self
    {
        $this->adress1 = $adress1;

        return $this;
    }

    public function getAdress2(): ?string
    {
        return $this->adress2;
    }

    public function setAdress2(?string $adress2): self
    {
        $this->adress2 = $adress2;

        return $this;
    }

    public function getAdress3(): ?string
    {
        return $this->adress3;
    }

    public function setAdress3(?string $adress3): self
    {
        $this->adress3 = $adress3;

        return $this;
    }

    /**
     * @return Collection|Type[]
     */
    public function getTypes(): Collection
    {
        return $this->types;
    }

    public function addType(Type $type): self
    {
        if (!$this->types->contains($type)) {
            $this->types[] = $type;
            $type->setConcours($this);
        }

        return $this;
    }

    public function removeType(Type $type): self
    {
        if ($this->types->removeElement($type)) {
            // set the owning side to null (unless already changed)
            if ($type->getConcours() === $this) {
                $type->setConcours(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Categorie[]
     */
    public function getCategorie(): Collection
    {
        return $this->categorie;
    }

    public function addCategorie(Categorie $categorie): self
    {
        if (!$this->categorie->contains($categorie)) {
            $this->categorie[] = $categorie;
            $categorie->setConcours($this);
        }

        return $this;
    }

    public function removeCategorie(Categorie $categorie): self
    {
        if ($this->categorie->removeElement($categorie)) {
            // set the owning side to null (unless already changed)
            if ($categorie->getConcours() === $this) {
                $categorie->setConcours(null);
            }
        }

        return $this;
    }
}
