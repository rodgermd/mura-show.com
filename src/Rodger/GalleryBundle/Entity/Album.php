<?php

namespace Rodger\GalleryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation\Timestampable;

/**
 * Rodger\GalleryBundle\Entity\Album
 *
 * @ORM\Table(name="albums")
 * @ORM\Entity(repositoryClass="Rodger\GalleryBundle\Entity\AlbumRepository")
 */
class Album {

  /**
   * @var integer $id
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @var string $name
   *
   * @ORM\Column(name="name", type="string", length=100)
   */
  private $name;

  /**
   * related images
   * @ORM\OneToMany(targetEntity="Image", mappedBy="Album")
   */
  private $Images;
  
  /**
   * Created at
   * @var datetime $created_at
   * @Timestampable(on="create")
   * @ORM\Column(name="created_at",type="datetime", nullable=true) 
   */
  private $created_at;

  /**
   * Is private flag
   * @var boolean $is_private
   * @ORM\Column(name="is_private", type="boolean")
   */
  private $is_private = false;

  public function __construct() {
    $this->Images = new ArrayCollection();
  }

  /**
   * Get id
   *
   * @return integer 
   */
  public function getId() {
    return $this->id;
  }

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
   * Gets is_provate flag
   * @return boolean 
   */
  public function getIsPrivate() {
    return $this->is_private;
  }

  /**
   * Sets is_private flag
   * @param boolean $is_private 
   */
  public function setIsPrivate($is_private) {
    $this->is_private = (bool) $is_private;
  }
  
  /**
   * Gets created at
   * @return datetime 
   */
  public function getCreatedAt()
  {
    return $this->created_at;
  }
  
  /**
   * Sets created at
   * @param datetime $created_at 
   */
  public function setCreatedAt($created_at)
  {
    $this->created_at = $created_at;
  }

}