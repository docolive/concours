<?php

namespace App\Entity;

use App\Repository\ProfilRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator as MyAssert;

/**
 * @ORM\Entity(repositoryClass=ProfilRepository::class)
 */
class Profil
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $raison_sociale;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $prenom;

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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $adress4;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $adress5;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="profil", cascade={"persist", "remove"})
     */
     
    private $user;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @MyAssert\ChecKSiret
     */
    private $siret;

    /**
     * @ORM\Column(type="boolean")
     */
    private $jure = false;

    /**
     * @ORM\ManyToMany(targetEntity=Categorie::class, inversedBy="jures")
     */
    private $choix_degustation;

    public function __construct()
    {
        $this->choix_degustation = new ArrayCollection();
    } //404 833 048 00022

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRaisonSociale(): ?string
    {
        return $this->raison_sociale;
    }

    public function setRaisonSociale(?string $raison_sociale): self
    {
        $this->raison_sociale = $raison_sociale;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;

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

    public function getAdress4(): ?string
    {
        return $this->adress4;
    }

    public function setAdress4(?string $adress4): self
    {
        $this->adress4 = $adress4;

        return $this;
    }

    public function getAdress5(): ?string
    {
        return $this->adress5;
    }

    public function setAdress5(?string $adress5): self
    {
        $this->adress5 = $adress5;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

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

    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function setSiret(?string $siret): self
    {
        $this->siret = $siret;

        return $this;
    }

    public function getJure(): ?bool
    {
        return $this->jure;
    }

    public function setJure(bool $jure): self
    {
        $this->jure = $jure;

        return $this;
    }

    /**
     * @return Collection|Categorie[]
     */
    public function getChoixDegustation(): Collection
    {
        return $this->choix_degustation;
    }

    public function addChoixDegustation(Categorie $choixDegustation): self
    {
        if (!$this->choix_degustation->contains($choixDegustation)) {
            $this->choix_degustation[] = $choixDegustation;
        }

        return $this;
    }

    public function removeChoixDegustation(Categorie $choixDegustation): self
    {
        $this->choix_degustation->removeElement($choixDegustation);

        return $this;
    }
}
