<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Etats
 *
 * @ORM\Table(name="etats")
 *
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="App\Repository\EtatsRepository")
 */
class Etats
{
    /**
     * @var int
     *
     * @ORM\Column(name="no_etat", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $noEtat;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=30, nullable=false)
     */
    private $libelle;

    /**
     * @return int
     */
    public function getNoEtat(): int
    {
        return $this->noEtat;
    }

    /**
     * @param int $noEtat
     */
    public function setNoEtat(int $noEtat): void
    {
        $this->noEtat = $noEtat;
    }

    /**
     * @return string
     */
    public function getLibelle(): string
    {
        return $this->libelle;
    }

    /**
     * @param string $libelle
     */
    public function setLibelle(string $libelle): void
    {
        $this->libelle = $libelle;
    }


    public function __toString():String{

        return $this->getLibelle();
    }


}
