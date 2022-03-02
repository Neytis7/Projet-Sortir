<?php

namespace App\Entity;

use App\Repository\ParticipantsRepository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use http\Client\Curl\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;


/**
 * Participants
 *
 * @ORM\Table(name="participants", uniqueConstraints={@ORM\UniqueConstraint(name="participants_pseudo_uk", columns={"pseudo"})})
 *
 *
 * @ORM\Entity(repositoryClass="App\Repository\ParticipantsRepository")
 *
 */
 class Participants  implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="no_participant", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $noParticipant;

    /**
     * @var string
     *
     * @ORM\Column(name="pseudo", type="string", length=30, nullable=false, unique=true)
     */
    private $pseudo;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=30, nullable=false)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=30, nullable=false)
     */
    private $prenom;

    /**
     * @var string|null
     *
     * @ORM\Column(name="telephone", type="string", length=15, nullable=true)
     */
    private $telephone;

    /**
     * @var string
     *
     * @ORM\Column(name="mail", type="string", length=20, nullable=false)
     */
    private $mail;

    /**
     * @var string
     *
     * @ORM\Column(name="mot_de_passe", type="string", length=100, nullable=false)
     */
    private $motDePasse;

    /**
     * @var bool
     *
     * @ORM\Column(name="administrateur", type="boolean", nullable=false)
     */
    private $administrateur;

    /**
     * @var bool
     *
     * @ORM\Column(name="actif", type="boolean", nullable=false)
     */
    private $actif;

    /**
     * @var int
     *
     * @ORM\Column(name="sites_no_site", type="integer", nullable=false)
     */
    private $sitesNoSite;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Sorties", mappedBy="participantsNoParticipant")
     */
    private $sortiesNoSortie;
     private $roles = [];

     /**
     * Constructor
     */
    public function __construct()
    {
        $this->sortiesNoSortie = new ArrayCollection();

    }

    /**
     * @return int
     */
    public function getNoParticipant(): int
    {
        return $this->noParticipant;
    }

    /**
     * @param int $noParticipant
     */
    public function setNoParticipant(int $noParticipant): void
    {
        $this->noParticipant = $noParticipant;
    }

    /**
     * @return string
     */
    public function getPseudo(): string
    {
        return $this->pseudo;
    }

    /**
     * @param string $pseudo
     */
    public function setPseudo(string $pseudo): void
    {
        $this->pseudo = $pseudo;
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
     * @return string
     */
    public function getPrenom(): string
    {
        return $this->prenom;
    }

    /**
     * @param string $prenom
     */
    public function setPrenom(string $prenom): void
    {
        $this->prenom = $prenom;
    }

    /**
     * @return string|null
     */
    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    /**
     * @param string|null $telephone
     */
    public function setTelephone(?string $telephone): void
    {
        $this->telephone = $telephone;
    }

    /**
     * @return string
     */
    public function getMail(): string
    {
        return $this->mail;
    }

    /**
     * @param string $mail
     */
    public function setMail(string $mail): void
    {
        $this->mail = $mail;
    }

    /**
     * @return string
     */
    public function getMotDePasse(): string
    {
        return $this->motDePasse;
    }

    /**
     * @param string $motDePasse
     */
    public function setMotDePasse(string $motDePasse): void
    {
        $this->motDePasse = $motDePasse;
    }

    /**
     * @return bool
     */
    public function isAdministrateur(): bool
    {
        return $this->administrateur;
    }

    /**
     * @param bool $administrateur
     */
    public function setAdministrateur(bool $administrateur): void
    {
        $this->administrateur = $administrateur;
    }

    /**
     * @return bool
     */
    public function isActif(): bool
    {
        return $this->actif;
    }

    /**
     * @param bool $actif
     */
    public function setActif(bool $actif): void
    {
        $this->actif = $actif;
    }

    /**
     * @return int
     */
    public function getSitesNoSite(): int
    {
        return $this->sitesNoSite;
    }

    /**
     * @param int $sitesNoSite
     */
    public function setSitesNoSite(int $sitesNoSite): void
    {
        $this->sitesNoSite = $sitesNoSite;
    }

    /**
     * @return Collection
     */
    public function getSortiesNoSortie(): ArrayCollection|Collection
    {
        return $this->sortiesNoSortie;
    }

    /**
     * @param Collection $sortiesNoSortie
     */
    public function setSortiesNoSortie(ArrayCollection|Collection $sortiesNoSortie): void
    {
        $this->sortiesNoSortie = $sortiesNoSortie;
    }

    public function getAdministrateur(): ?bool
    {
        return $this->administrateur;
    }

    public function getActif(): ?bool
    {
        return $this->actif;
    }

    public function addSortiesNoSortie(Sorties $sortiesNoSortie): self
    {
        if (!$this->sortiesNoSortie->contains($sortiesNoSortie)) {
            $this->sortiesNoSortie[] = $sortiesNoSortie;
            $sortiesNoSortie->addParticipantsNoParticipant($this);
        }

        return $this;
    }

    public function removeSortiesNoSortie(Sorties $sortiesNoSortie): self
    {
        if ($this->sortiesNoSortie->removeElement($sortiesNoSortie)) {
            $sortiesNoSortie->removeParticipantsNoParticipant($this);
        }

        return $this;
    }

     /**
      * @see PasswordAuthenticatedUserInterface
      */
     public function getPassword(): string
     {
         return $this->motDePasse;
     }

     public function getSalt()
     {
         // TODO: Implement getSalt() method.
     }

     public function eraseCredentials()
     {
         // TODO: Implement eraseCredentials() method.
     }

     public function getUsername()
     {
         // TODO: Implement getUsername() method.
     }

     public function __call(string $name, array $arguments)
     {
         // TODO: Implement @method string getUserIdentifier()
     }

     public function getRoles(): array
     {
         $roles = $this->roles;
         // guarantee every user at least has ROLE_USER
         $roles[] = 'ROLE_USER';
         return array_unique($roles);
     }
     public function getUserIdentifier(): String
     {
         return (string) $this->pseudo;
     }

 }
