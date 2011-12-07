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
  public function getLatestInAlbumQueryBuilder(Album $album, $show_private = false) {
    $qb = $this->createQueryBuilder('i')
      ->select('partial i.{id, filename}')
      ->where('i.album_id = :album_id');
    if (!$show_private) {
      $qb->andWhere('i.is_private = false');
    }
    
    $qb->orderBy('i.taken_at', 'desc')
       ->addOrderBy('i.uploaded_at', 'desc')
       ->setParameter('album_id', $album->getId());
    
    return $qb;
  }
}