<?php

namespace FormationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Bane
 *
 * @ORM\Table(name="bane")
 * @ORM\Entity(repositoryClass="FormationBundle\Repository\BaneRepository")
 */
class Bane
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="idFormation", type="integer")
     */
    private $idFormation;

    /**
     * @var int
     *
     * @ORM\Column(name="idUser", type="integer")
     */
    private $idUser;

    /**
     * @var bool
     *
     * @ORM\Column(name="bane", type="boolean")
     */
    private $bane;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set idFormation
     *
     * @param integer $idFormation
     *
     * @return Bane
     */
    public function setIdFormation($idFormation)
    {
        $this->idFormation = $idFormation;

        return $this;
    }

    /**
     * Get idFormation
     *
     * @return int
     */
    public function getIdFormation()
    {
        return $this->idFormation;
    }

    /**
     * Set idUser
     *
     * @param integer $idUser
     *
     * @return Bane
     */
    public function setIdUser($idUser)
    {
        $this->idUser = $idUser;

        return $this;
    }

    /**
     * Get idUser
     *
     * @return int
     */
    public function getIdUser()
    {
        return $this->idUser;
    }

    /**
     * Set bane
     *
     * @param boolean $bane
     *
     * @return Bane
     */
    public function setBane($bane)
    {
        $this->bane = $bane;

        return $this;
    }

    /**
     * Get bane
     *
     * @return bool
     */
    public function getBane()
    {
        return $this->bane;
    }
}

