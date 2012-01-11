<?php

namespace Rodger\GalleryBundle\Entity;

use Doctrine\ORM\EntityRepository, Doctrine\ORM\Query;
use FOS\UserBundle\Model\UserInterface;

/**
 * TagRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TagRepository extends EntityRepository
{
  /**
   * Searches Tags like givem
   * @param string $name
   * @return array - Tags
   */
  public function search($name)
  {
    $name = strtolower($name);
    $qb = $this->createQueryBuilder('t');
    return $qb
            ->select('t')
            ->where($qb->expr()->like('t.name', $qb->expr()->literal($name . '%')))
            ->getQuery()
            ->getResult();
  }
  
  /**
   * Gets existing or creates new Tag
   * @return Tag
   */
  public function getOrCreate($name) {
    $name = trim(strtolower($name));
    $tag = $this->find($name);
    if (!$tag) {
      $tag = new Tag();
      $tag->setName($name);
      $this->_em->persist($tag);
    }
    
    return $tag;
  }
  
  /**
   * Gets Tags using filters
   * @param integer $year
   * @param array $use_tags
   * @param UserInterface $user
   * @return array 
   */
  public function getFilteredAlbumsTags($user, $year = null, array $use_tags = array()) {
    $qb = $this->createQueryBuilder('t')->select('t')->orderBy('t.name');
    
    if (is_numeric($year)) {
      $qb1 = $this->createQueryBuilder('t');
          $qb1->select('t')
              ->innerJoin('t.Images', 'i', 'WITH', $qb1->expr()->eq('i.year', $year))
              ->innerJoin('i.Album', 'a')
              ->groupBy('t.name');
      if (!$user) $qb1->andWhere('i.is_private = false AND a.is_private = false');
              
      $result1 = array_unique(array_map(function($t){ return $t['name'];}, $qb1->getQuery()->execute(array(), Query::HYDRATE_ARRAY)));
      
      $qb2 = $this->createQueryBuilder('t');
          $qb2->select('t')
              ->innerJoin('t.Albums', 'a')
              ->innerJoin('a.Images', 'i', 'WITH', $qb2->expr()->eq('i.year', $year))
              ->groupBy('t.name');
      if (!$user) $qb2->andWhere('i.is_private = false AND a.is_private = false');
      $result2 = array_unique(array_map(function($t){ return $t['name'];}, $qb2->getQuery()->execute(array(), Query::HYDRATE_ARRAY)));
      
      $qb->andWhere($qb->expr()->in('t.name', array_unique($result1 + $result2)));
    }
    
    
    
    return $qb->getQuery()->execute();
  }
  
  /**
   * Gets Album images Tags using filters
   * @param integer $year
   * @param array $use_tags
   * @param UserInterface $user
   * @return array 
   */
  public function getFilteredAlbumImagesTags(Album $album, $user, array $filters) {
    $qb = $this->createQueryBuilder('t')
            ->select('t')
            ->innerJoin('t.Images', 'i')
            ->groupBy('t.name')
            ->orderBy('t.name');
    $qb->andWhere($qb->expr()->eq('i.album_id', $album->getId()));
    
    if (is_numeric($filters['year'])) $qb->andWhere($qb->expr()->eq('i.year', $filters['year']));
    if (!$user instanceof UserInterface) $qb->andWhere('i.is_private = false');
    
    return $qb->getQuery()->execute();
  }
}