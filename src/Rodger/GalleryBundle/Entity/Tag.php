<?php

namespace Rodger\GalleryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Rodger\GalleryBundle\Entity\Tag
 *
 * @ORM\Table(name="tags")
 * @ORM\Entity(repositoryClass="Rodger\GalleryBundle\Entity\TagRepository")
 */
class Tag {

  /**
   * @var string $name
   * @ORM\Id
   * @ORM\Column(name="name", type="string", length=50)
   */
  private $name;
  
  /**
   * @ORM\ManyToMany(targetEntity="Image", mappedBy="Tags")
   * @var array Images 
   */
  private $Images;
  
  /**
   * @ORM\ManyToMany(targetEntity="Album", mappedBy="Tags")
   * @var array Albums 
   */
  private $Albums;

  /**
   * Set name
   *
   * @param string $name
   */
  public function setName($name) {
    $this->name = $name;
  }

  /**
   * Get name
   *
   * @return string 
   */
  public function getName() {
    return $this->name;
  }

  /**
   * Gets related Images
   * @return array 
   */
  public function getImages()
  {
    return $this->Images;
  }
  
  /**
   * Sets related Images
   * @param array $images 
   */
  public function setImages($images) {
    $this->Images = $images;
  }
  
  /**
   * Add Image into collection
   * @param Image $image 
   */
  public function addImage(Image $image)
  {
    $this->Images[] = $image;
  }
  
  /**
   * Gets related Albums
   * @return array 
   */
  public function getAlbums()
  {
    return $this->Albums;
  }
  
  /**
   * Sets related Albums
   * @param array $albums 
   */
  public function setAlbums($albums) {
    $this->Albums = $albums;
  }
  
  /**
   * Add Album into collection
   * @param Album $Album 
   */
  public function addAlbum(Album $album)
  {
    $this->Albums[] = $album;
  }
}