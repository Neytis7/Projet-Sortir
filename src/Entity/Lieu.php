<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Lieu
 *
 * @ORM\Table(name="lieu", indexes={@ORM\Index(name="lieu_villes_fk", columns={"nom"})})
 * @ORM\Entity(repositoryClass="App\Repository\LieuRepository")
 */
class Lieu
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=30, nullable=false)
     */
    private string $nom;

    /**
     * @var string|null
     *
     * @ORM\Column(name="rue", type="string", length=30, nullable=true)
     */
    private ?string $rue;

    /**
     * @var float|null
     *
     * @ORM\Column(name="latitude", type="float", precision=10, scale=0, nullable=true)
     */
    private ?float $latitude;

    /**
     * @var float|null
     *
     * @ORM\Column(name="longitude", type="float", precision=10, scale=0, nullable=true)
     */
    private ?float $longitude;

    /**
     * @var Ville
     *
     * @ORM\ManyToOne(targetEntity="Ville")
     * * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ville_id", referencedColumnName="id",nullable=false)
     * })
     */
    private Ville $ville;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Lieu
     */
    public function setId(int $id): Lieu
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNom(): ?string
    {
        return $this->nom;
    }

    /**
     * @param string $nom
     * @return Lieu
     */
    public function setNom(string $nom): Lieu
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getRue(): ?string
    {
        return $this->rue;
    }

    /**
     * @param string|null $rue
     * @return Lieu
     */
    public function setRue(?string $rue): Lieu
    {
        $this->rue = $rue;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    /**
     * @param float|null $latitude
     * @return Lieu
     */
    public function setLatitude(?float $latitude): Lieu
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    /**
     * @param float|null $longitude
     * @return Lieu
     */
    public function setLongitude(?float $longitude): Lieu
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * @return Ville|null
     */
    public function getVille(): ?Ville
    {
        return $this->ville;
    }

    /**
     * @param Ville $ville
     * @return Lieu
     */
    public function setVille(Ville $ville): Lieu
    {
        $this->ville = $ville;

        return $this;
    }

    public function __toString():String{

        return $this->getNom()."-".$this->getVille()->getNom();
    }
}
