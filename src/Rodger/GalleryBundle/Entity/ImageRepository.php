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
    $qb = $this->getAccessibleImagesBuilder($album, $show_private);
    $qb->select('partial i.{id, filename}')
          ->orderBy('i.taken_at', 'desc')
          ->addOrderBy('i.uploaded_at', 'desc');
    
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
          ->where('i.album_id = :album_id')
          ->setParameter('album_id', $album->getId())
          ->orderBy('i.taken_at', 'asc')
          ->addOrderBy('i.uploaded_at', 'asc');
    
    if (!$user) {
      $qb->andWhere('i.is_private = false');
    }
    return $qb;
  }
  
  public function getYears($user) {
    $show_private = ($user instanceof \FOS\UserBundle\Model\UserInterface);
    $qb = $this->createQueryBuilder('i');
    if (!$show_private) {
      $qb->where('i.is_private = false');
    }
    $qb->select('DISTINCT i.year')->orderBy('i.year', 'asc');
   
    $result = $qb->getQuery()->getResult();
    return array_map(function($item){ return $item['year']; }, $result);
            
  }
}