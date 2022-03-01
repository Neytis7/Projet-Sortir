<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * Sorties
 *
 * @ORM\Table(name="sorties", indexes={@ORM\Index(name="sorties_lieux_fk", columns={"lieux_no_lieu"}), @ORM\Index(name="sorties_etats_fk", columns={"etats_no_etat"}), @ORM\Index(name="sorties_participants_fk", columns={"organisateur"})})
 * @ORM\Entity(repositoryClass="App\Repository\SortiesRepository")
 */
class Sorties
{
    /**
     * @var int
     *
     * @ORM\Column(name="no_sortie", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $noSortie;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=30, nullable=false)
     */
    private $nom;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="datedebut", type="datetime", nullable=false)
     */
    private $datedebut;

    /**
     * @var int|null
     *
     * @ORM\Column(name="duree", type="integer", nullable=true)
     */
    private $duree;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="datecloture", type="datetime", nullable=false)
     */
    private $datecloture;

    /**
     * @var int
     *
     * @ORM\Column(name="nbinscriptionsmax", type="integer", nullable=false)
     */
    private $nbinscriptionsmax;

    /**
     * @var string|null
     *
     * @ORM\Column(name="descriptioninfos", type="string", length=500, nullable=true)
     */
    private $descriptioninfos;


    /**
     * @var Etats
     *
     * @ORM\Column(name="urlPhoto", type="string", length=250, nullable=true)
     */
    private $urlphoto;

    /**
     * @var Etats
     *
     * @ORM\ManyToOne(targetEntity="Etats")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="etats_no_etat", referencedColumnName="no_etat")
     * })
     */
    private $etatsNoEtat;

    /**
     * @var Participants
     *
     * @ORM\ManyToOne(targetEntity="Participants")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="organisateur", referencedColumnName="no_participant")
     * })
     */
    private $organisateur;

    /**
     * @var Lieux
     *
     * @ORM\ManyToOne(targetEntity="Lieux")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="lieux_no_lieu", referencedColumnName="no_lieu")
     * })
     */
    private $lieuxNoLieu;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Participants", inversedBy="sortiesNoSortie")
     * @ORM\JoinTable(name="inscriptions",
     *   joinColumns={
     *     @ORM\JoinColumn(name="sorties_no_sortie", referencedColumnName="no_sortie")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="participants_no_participant", referencedColumnName="no_participant")
     *   }
     * )
     */
    private $participantsNoParticipant;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->participantsNoParticipant = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getNoSortie(): int
    {
        return $this->noSortie;
    }

    /**
     * @param int $noSortie
     */
    public function setNoSortie(int $noSortie): void
    {
        $this->noSortie = $noSortie;
    }

    /**
     * @return string
     */
    public function getNom(): string
    {
        return $this->nom;
    }

    /**
     * @param string $nom
     */
    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    /**
     * @return DateTime
     */
    public function getDatedebut(): DateTime
    {
        return $this->datedebut;
    }

    /**
     * @param DateTime $datedebut
     */
    public function setDatedebut(DateTime $datedebut): void
    {
        $this->datedebut = $datedebut;
    }

    /**
     * @return int|null
     */
    public function getDuree(): ?int
    {
        return $this->duree;
    }

    /**
     * @param int|null $duree
     */
    public function setDuree(?int $duree): void
    {
        $this->duree = $duree;
    }

    /**
     * @return DateTime
     */
    public function getDatecloture(): DateTime
    {
        return $this->datecloture;
    }

    /**
     * @param DateTime $datecloture
     */
    public function setDatecloture(DateTime $datecloture): void
    {
        $this->datecloture = $datecloture;
    }

    /**
     * @return int
     */
    public function getNbinscriptionsmax(): int
    {
        return $this->nbinscriptionsmax;
    }

    /**
     * @param int $nbinscriptionsmax
     */
    public function setNbinscriptionsmax(int $nbinscriptionsmax): void
    {
        $this->nbinscriptionsmax = $nbinscriptionsmax;
    }

    /**
     * @return string|null
     */
    public function getDescriptioninfos(): ?string
    {
        return $this->descriptioninfos;
    }

    /**
     * @param string|null $descriptioninfos
     */
    public function setDescriptioninfos(?string $descriptioninfos): void
    {
        $this->descriptioninfos = $descriptioninfos;
    }


    /**
     * @return Etats
     */
    public function getEtatsNoEtat(): ?Etats
    {
        return $this->etatsNoEtat;
    }

    /**
     * @param Etats $etatsNoEtat
     */
    public function setEtatsNoEtat(Etats $etatsNoEtat): void
    {
        $this->etatsNoEtat = $etatsNoEtat;
    }

    /**
     * @return Participants
     */
    public function getOrganisateur(): ?Participants
    {
        return $this->organisateur;
    }

    /**
     * @param Participants $organisateur
     */
    public function setOrganisateur(Participants $organisateur): void
    {
        $this->organisateur = $organisateur;
    }

    /**
     * @return Lieux
     */
    public function getLieuxNoLieu(): ?Lieux
    {
        return $this->lieuxNoLieu;
    }

    /**
     * @param Lieux $lieuxNoLieu
     */
    public function setLieuxNoLieu(Lieux $lieuxNoLieu): void
    {
        $this->lieuxNoLieu = $lieuxNoLieu;
    }

    /**
     * @return Collection
     */
    public function getParticipantsNoParticipant(): ArrayCollection|Collection
    {
        return $this->participantsNoParticipant;
    }

    /**
     * @param Collection $participantsNoParticipant
     */
    public function setParticipantsNoParticipant(ArrayCollection|Collection $participantsNoParticipant): void
    {
        $this->participantsNoParticipant = $participantsNoParticipant;
    }

    public function addParticipantsNoParticipant(Participants $participantsNoParticipant): self
    {
        if (!$this->participantsNoParticipant->contains($participantsNoParticipant)) {
            $this->participantsNoParticipant[] = $participantsNoParticipant;
        }

        return $this;
    }

    public function removeParticipantsNoParticipant(Participants $participantsNoParticipant): self
    {
        $this->participantsNoParticipant->removeElement($participantsNoParticipant);

        return $this;
    }


}
