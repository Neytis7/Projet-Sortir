<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\Json;

/**
 * Participant
 *
 * @ORM\Table(name="participant", uniqueConstraints={@ORM\UniqueConstraint(name="participant_pseudo_uk", columns={"pseudo"})})
 *
 *
 * @ORM\Entity(repositoryClass="App\Repository\ParticipantRepository")
 *
 */
#[UniqueEntity('mail', message: 'ce mail est déjà utilisé.')]
#[UniqueEntity('pseudo', message: 'ce pseudo est déjà utilisé.')]
 class Participant implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @var int|null
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private ?int $id = null;

    /**
     * @ORM\OneToMany(targetEntity=ResetPasswordRequest::class, mappedBy="user")
     */
    private Collection $resetPasswordRequests;


    /**
     * @var string
     *
     * @ORM\Column(name="pseudo", type="string", length=30, nullable=false, unique=true)
     */
    private string $pseudo;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=30, nullable=false)
     */
    private string $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=30, nullable=false)
     */
    private string $prenom;

    /**
     * @var string|null
     *
     * @ORM\Column(name="telephone", type="string", length=15, nullable=true)
     */
    private ?string $telephone;

    /**
     * @var string
     *
     * @ORM\Column(name="mail", type="string", length=50, nullable=false, unique=true)
     */
    private string $mail;

    /**
     * @var string
     *
     * @ORM\Column(name="mot_de_passe", type="string", length=100, nullable=false)
     */
    private string $motDePasse;

    /**
     * @var bool
     *
     * @ORM\Column(name="administrateur", type="boolean", nullable=false)
     */
    private bool $administrateur;

    /**
     * @var bool
     *
     * @ORM\Column(name="actif", type="boolean", nullable=false)
     */
    private bool $actif;
    
    /**
     * @var Site
     * @ORM\ManyToOne(targetEntity="Site")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="site", referencedColumnName="id",nullable=false)
     * })
     */
    private Site $site;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Sortie", inversedBy="participants")
     */
    private Collection $sorties;

    /**
     * @ORM\Column(type="json")
     */
     private $roles = [];

     /**
      * @ORM\Column(type="string", length=150, nullable=true)
      */
     private $photo;

     /**
     * Constructor
     */
    public function __construct()
    {
        $this->sorties = new ArrayCollection();
        $this->resetPasswordRequests = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Participant
     */
    public function setId(int $id): Participant
    {
        $this->id = $id;

        return $this;
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
     * @return Participant
     */
    public function setPseudo(string $pseudo): Participant
    {
        $this->pseudo = $pseudo;

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
     * @return Participant
     */
    public function setNom(string $nom): Participant
    {
        $this->nom = $nom;

        return $this;
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
     * @return Participant
     */
    public function setPrenom(string $prenom): Participant
    {
        $this->prenom = $prenom;

        return $this;
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
     * @return Participant
     */
    public function setTelephone(?string $telephone): Participant
    {
        $this->telephone = $telephone;

        return $this;
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
     * @return Participant
     */
    public function setMail(string $mail): Participant
    {
        $this->mail = $mail;

        return $this;
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
     * @return Participant
     */
    public function setMotDePasse(string $motDePasse): Participant
    {
        $this->motDePasse = $motDePasse;

        return $this;
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
     * @return Participant
     */
    public function setAdministrateur(bool $administrateur): Participant
    {
        $this->administrateur = $administrateur;

        return $this;
    }

    /**
     * @return bool
     */
    public function isActif(): bool
    {
        return $this->actif;
    }

    public function getActif(): ?bool
    {
        return $this->actif;
    }

    /**
     * @param bool $actif
     * @return Participant
     */
    public function setActif(bool $actif): Participant
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * @return Site
     */
    public function getSite(): Site
    {
        return $this->site;
    }

    /**
     * @param Site $site
     * @return Participant
     */
    public function setSite(Site $site): Participant
    {
        $this->site = $site;

        return $this;
    }

    /**
     * @return ArrayCollection|Collection
     */
    public function getSorties(): ArrayCollection|Collection
    {
        return $this->sorties;
    }

    public function addSortie(Sortie $sortie): Participant
    {
        if (!$this->sorties->contains($sortie)) {
            $this->sorties[] = $sortie;
            $sortie->addParticipant($this);
        }

        return $this;
    }

    public function removeSortie(Sortie $sortie): Participant
    {
        if ($this->sorties->removeElement($sortie)) {
            $sortie->removeParticipant($this);
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
         return $this->pseudo;
     }



     public function setRoles(array $roles): self
     {
         $this->roles = $roles;

         return $this;
     }

     public function getRoles(): array
     {
         $roles = $this->roles;
         $roles[] = json_encode($this->roles);
         // guarantee every user at least has ROLE_USER
         //$roles[] = 'ROLE_USER';
         return array_unique($roles);
     }
     public function getUserIdentifier(): String
     {
         return (string) $this->pseudo;
     }

     public function getAdministrateur(): ?bool
     {
         return $this->administrateur;
     }

     public function addSorty(Sortie $sorty): self
     {
         if (!$this->sorties->contains($sorty)) {
             $this->sorties[] = $sorty;
         }

         return $this;
     }

     public function removeSorty(Sortie $sorty): self
     {
         $this->sorties->removeElement($sorty);

         return $this;
     }

     public function getPhoto(): ?string
     {
         return $this->photo;
     }

     public function setPhoto(?string $photo): self
     {
         $this->photo = $photo;

         return $this;
     }
 }
