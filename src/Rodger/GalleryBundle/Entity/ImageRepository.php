<?php

namespace Rodger\GalleryBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ImageRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ImageRepository extends EntityRepository
{
    public function getLatestInAlbumQueryBuilder(Album $album, $show_private = false, array $filters = array())
    {
        $qb = $this->getAccessibleImagesBuilder($album, $show_private);
        $qb->select('i, a')
            ->innerJoin('i.album', 'a')
            ->orderBy('i.takenAt', 'desc')
            ->addOrderBy('i.uploadedAt', 'desc');

        if (count($filters)) {
            if (is_numeric($filters['year'])) {
                $qb->andWhere($qb->expr()->eq('i.year', $filters['year']));
            }
            if (count($filters['tags'])) {
                $qb->innerJoin('i.tags', 't', 'WITH', $qb->expr()->in('t.name', $filters['tags']));
            }
        }

        return $qb;
    }

    /**
     * Gets asccessible image QueryBuilder
     * @param Album $album
     * @param type $user
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getAccessibleImagesBuilder(Album $album, $user = null)
    {
        $qb = $this->createQueryBuilder('i')
            ->where('i.album = :album')
            ->setParameter('album', $album->getId())
            ->orderBy('i.takenAt', 'asc')
            ->addOrderBy('i.uploadedAt', 'asc');

        if (!$user) {
            $qb->andWhere('i.private = false');
        }

        return $qb;
    }

    public function getYears($user)
    {
        $show_private = ($user instanceof \FOS\UserBundle\Model\UserInterface);
        $qb = $this->createQueryBuilder('i');
        if (!$show_private) {
            $qb->where('i.private = false');
        }
        $qb->select('DISTINCT i.year')->orderBy('i.year', 'desc');

        $result = $qb->getQuery()->getResult();

        return array_map(
            function ($item) {
                return $item['year'];
            },
            $result
        );

    }

    public function getFilteredAlbumImages(Album $album, array $filters, $user)
    {
        $qb = $this->getAccessibleImagesBuilder($album, $user);
        if (count($filters['tags'])) {
            $qb->innerJoin('i.album', 'a')
                ->leftJoin('a.tags', 'at')
                ->leftJoin('i.tags', 'it')
                ->andWhere(
                    $qb->expr()->orX(
                        $qb->expr()->in('it.name', $filters['tags']),
                        $qb->expr()->in('at.name', $filters['tags'])
                    )
                );

        }

        return $qb->getQuery()->execute();
    }
}