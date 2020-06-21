<?php
// src/AppBundle/Entity/User.php

namespace FOSBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * @return string
     */
    public function getTypecompte()
    {
        return $this->typecompte;
    }

    /**
     * @param string $typecompte
     */
    public function setTypecompte($typecompte)
    {
        $this->typecompte = $typecompte;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="typecompte", type="string", length=255)
     */
    private $typecompte;



    /**
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * @param string $prenom
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;
    }

    /**
     * @return int
     */
    public function getNumtel()
    {
        return $this->numtel;
    }

    /**
     * @param int $numtel
     */
    public function setNumtel($numtel)
    {
        $this->numtel = $numtel;
    }





    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=255)
     */
    private $prenom="prenom";

    /**
     * @var integer
     *
     * @ORM\Column(name="numtel", type="integer")
     */
    private $numtel=58059894;










    public function __construct()
    {
        parent::__construct();
        // your own logic
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

}