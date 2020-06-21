<?php

namespace ForumBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * publication
 *
 * @ORM\Table(name="publication")
 * @ORM\Entity(repositoryClass="ForumBundle\Repository\publicationRepository")
 */
class publication
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
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var float
     *
     * @ORM\Column(name="note", type="float")
     */
    private $note=0;



    /**
     * @var boolean
     *
     * @ORM\Column(name="resolu", type="boolean", length=255)
     */
    private $resolu;

    /**
     * @var string
     *
     * @ORM\Column(name="typeChasse", type="string", length=255)
     */
    private $typeChasse;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="FOSBundle\Entity\User")
     * @ORM\JoinColumn(name="iduser",nullable=true, referencedColumnName="id", onDelete="CASCADE")
     */
    private $idUser;

    /**
     * @return float
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param float $note
     */
    public function setNote($note)
    {
        $this->note = $note;
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
     * Set description
     *
     * @param string $description
     *
     * @return publication
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set typeChasse
     *
     * @param string $typeChasse
     *
     * @return publication
     */
    public function setTypeChasse($typeChasse)
    {
        $this->typeChasse = $typeChasse;

        return $this;
    }

    /**
     * Get typeChasse
     *
     * @return string
     */
    public function getTypeChasse()
    {
        return $this->typeChasse;
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
     * @return bool
     */
    public function isResolu()
    {
        return $this->resolu;
    }

    /**
     * @param bool $resolu
     */
    public function setResolu($resolu)
    {
        $this->resolu = $resolu;
    }






}

