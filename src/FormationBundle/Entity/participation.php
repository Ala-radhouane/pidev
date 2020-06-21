<?php

namespace FormationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * participation
 *
 * @ORM\Table(name="participation")
 * @ORM\Entity(repositoryClass="FormationBundle\Repository\participationRepository")
 */
class participation
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;



    private $bane;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="FOSBundle\Entity\User")
     * @ORM\JoinColumn(name="iduser",nullable=true, referencedColumnName="id", onDelete="CASCADE")
     */
    private $idUser;

    /**
     * @var Formation
     * @ORM\ManyToOne(targetEntity="FormationBundle\Entity\Formation")
     * @ORM\JoinColumn(name="idformation",nullable=true, referencedColumnName="id", onDelete="CASCADE")
     */
    private $idFormation;

    /**
     * @return mixed
     */
    public function getBane()
    {
        return $this->bane;
    }

    /**
     * @param mixed $bane
     */
    public function setBane($bane)
    {
        $this->bane = $bane;
    }



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
     * @return User
     */
    public function getIdUser()
    {
        return $this->idUser;
    }

    /**
     * @param User $idUser
     */
    public function setIdUser($idUser)
    {
        $this->idUser = $idUser;
    }

    /**
     * @return Formation
     */
    public function getIdFormation()
    {
        return $this->idFormation;
    }

    /**
     * @param Formation $idFormation
     */
    public function setIdFormation($idFormation)
    {
        $this->idFormation = $idFormation;
    }










}

