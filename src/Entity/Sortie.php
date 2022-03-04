<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * Sortie
 *
 * @ORM\Table(name="sortie", indexes={@ORM\Index(name="sortie_lieu_fk", columns={"id"}), @ORM\Index(name="sortie_etat_fk", columns={"id"}), @ORM\Index(name="sortie_participant_fk", columns={"organisateur"})})
 * @ORM\Entity(repositoryClass="App\Repository\SortieRepository")
 */
class Sortie
{

    const ETAT_CREEE = 'Créée';
    const ETAT_ANNULEE = 'Annulée';
    const ETAT_ARCHIVEE = 'Archivée';
    const ETAT_OUVERTE = 'Ouverte';
    const ETAT_TERMINEE = 'Terminée';
    const ETAT_CLOTUREE = 'Cloturée';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="motif_annulation", type="string", length=500, nullable=true)
     */
    private ?string $motifAnnulation;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=30, nullable=false)
     */
    private string $nom;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="date_debut", type="datetime", nullable=false)
     */
    private DateTime $dateDebut;

    /**
     * @var int
     *
     * @ORM\Column(name="duree", type="integer", nullable=false)
     */
    private ?int $duree;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="date_cloture", type="datetime", nullable=false)
     */
    private DateTime $dateCloture;

    /**
     * @var int
     *
     * @ORM\Column(name="nb_inscriptions_max", type="integer", nullable=false)
     */
    private int $nbInscriptionsMax;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description_infos", type="string", length=500, nullable=true)
     */
    private ?string $descriptionInfos;

    /**
     * @var string|null
     *
     * @ORM\Column(name="url_photo", type="string", length=250, nullable=true)
     */
    private ?string $urlPhoto = null;

    /**
     * @var Etat
     *
     * @ORM\ManyToOne(targetEntity="Etat")
     * * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="etat_id", referencedColumnName="id",nullable=false)
     * })
     */
    private Etat $etat;

    /**
     * @var Participant
     *
     * @ORM\ManyToOne(targetEntity="Participant")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="organisateur", referencedColumnName="id",nullable=false)
     * })
     */
    private Participant $organisateur;

    /**
     * @var Lieu
     *
     * @ORM\ManyToOne(targetEntity="Lieu")
     * * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="lieu_id", referencedColumnName="id",nullable=false)
     * })
     */
    private Lieu $lieu;

    /**
     *
     * @ORM\ManyToMany(targetEntity="Participant", mappedBy="sorties")
     */
    private $participants;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->participants = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Sortie
     */
    public function setId(int $id): Sortie
    {
        $this->id = $id;

        return $this;
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
     * @return Sortie
     */
    public function setNom(string $nom): Sortie
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDatedebut(): DateTime
    {
        return $this->dateDebut;
    }

    /**
     * @param DateTime $dateDebut
     * @return Sortie
     */
    public function setDatedebut(DateTime $dateDebut): Sortie
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    /**
     * @return int
     */
    public function getDuree(): int
    {
        return $this->duree;
    }

    /**
     * @param int $duree
     * @return Sortie
     */
    public function setDuree(int $duree): Sortie
    {
        $this->duree = $duree;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDatecloture(): DateTime
    {
        return $this->dateCloture;
    }

    /**
     * @param DateTime $dateCloture
     * @return Sortie
     */
    public function setDatecloture(DateTime $dateCloture): Sortie
    {
        $this->dateCloture = $dateCloture;

        return $this;
    }

    /**
     * @return int
     */
    public function getNbInscriptionsMax(): int
    {
        return $this->nbInscriptionsMax;
    }

    /**
     * @param int $nbInscriptionsMax
     * @return Sortie
     */
    public function setNbinscriptionsmax(int $nbInscriptionsMax): Sortie
    {
        $this->nbInscriptionsMax = $nbInscriptionsMax;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescriptioninfos(): ?string
    {
        return $this->descriptionInfos;
    }

    /**
     * @param string|null $descriptionInfos
     * @return Sortie
     */
    public function setDescriptioninfos(?string $descriptionInfos): Sortie
    {
        $this->descriptionInfos = $descriptionInfos;

        return $this;
    }

    /**
     * @return Etat|null
     */
    public function getEtat(): ?Etat
    {
        return $this->etat;
    }

    /**
     * @param Etat $etat
     * @return Sortie
     */
    public function setEtat(Etat $etat): Sortie
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * @return Participant|null
     */
    public function getOrganisateur(): ?Participant
    {
        return $this->organisateur;
    }

    /**
     * @param Participant $organisateur
     * @return Sortie
     */
    public function setOrganisateur(Participant $organisateur): Sortie
    {
        $this->organisateur = $organisateur;

        return  $this;
    }

    /**
     * @return Lieu|null
     */
    public function getLieu(): ?Lieu
    {
        return $this->lieu;
    }

    /**
     * @param Lieu $lieu
     * @return Sortie
     */
    public function setLieu(Lieu $lieu): Sortie
    {
        $this->lieu = $lieu;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(Participant $participant): Sortie
    {
        if (!$this->participants->contains($participant)) {
            $this->participants[] = $participant;
        }

        return $this;
    }

    public function removeParticipant(Participant $participant): Sortie
    {
        $this->participants->removeElement($participant);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMotifAnnulation(): ?string
    {
        return $this->motifAnnulation;
    }

    /**
     * @param string|null $motifAnnulation
     * @return Sortie
     */
    public function setMotifAnnulation(?string $motifAnnulation): Sortie
    {
        $this->motifAnnulation = $motifAnnulation;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUrlphoto(): ?string
    {
        return $this->urlPhoto;
    }

    /**
     * @param string $urlPhoto
     */
    public function setUrlphoto(string $urlPhoto): void
    {
        $this->urlPhoto = $urlPhoto;
    }
}
