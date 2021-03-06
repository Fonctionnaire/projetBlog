<?php

namespace AppBundle\Repository;

/**
 * EpisodeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EpisodeRepository extends \Doctrine\ORM\EntityRepository
{

    public function getEpisodeWithFirstComments($id)
    {

        $qb = $this
            ->createQueryBuilder('e')
            ->addSelect('c', 'child')
            ->leftJoin('e.commentaires', 'c')
            ->leftJoin('c.children', 'child')
            ->where('c.parent IS NULL')
            ->andWhere('e.id = :id')
            ->setParameter('id', $id)
            ;

        return $qb
            ->getQuery()
            ->getSingleResult();

    }

}
