<?php

namespace ForumBundle\Repository;

/**
 * publicationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class publicationRepository extends \Doctrine\ORM\EntityRepository
{

    public function findDQL($iduser)
    {
        $q = $this->getEntityManager()
            ->createQuery("SELECT e FROM ForumBundle:publication e
            WHERE e.id=$iduser");
        return $q->getResult();

    }

}
