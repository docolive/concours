<?php

namespace App\Entity;

use App\Repository\EchantillonRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator as MyAssert;

/**
 * @ORM\Entity(repositoryClass=EchantillonRepository::class)
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
     * @MyAssert\NbreMaxEch
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
     * @Assert\IsTrue(message="Merci d'indiquer le nom de la variété d'olives de table.")
     */
    public function isVarietyWithTable()
    {
        if($this->categorie->getType()->getOtable() == true && empty($this->variety)){
            return false;
        }else{
            return true;
        }
    }

    /**
     * @Assert\IsTrue(message="Le volume du lot est inférieur au volume minimal dans cette catégorie.")
     */
    public function isVolOK()
    {
        if($this->categorie->getType()->getVolMinLot() > $this->volume){
            return false;
        }else{
            return true;
        }
    }



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
}
