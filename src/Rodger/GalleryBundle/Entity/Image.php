<?php

namespace Rodger\GalleryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation\Timestampable;

/**
 * Rodger\GalleryBundle\Entity\Image
 *
 * @ORM\Table(name="images")
 * @ORM\Entity(repositoryClass="Rodger\GalleryBundle\Entity\ImageRepository")
 */
class Image {

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
   * @var string $filename
   *
   * @ORM\Column(name="filename", type="string", length=50)
   */
  private $filename;
  
  
  /**
   * Uploaded at
   * @var datetime $uploaded_at
   * @Timestampable(on="create")
   * @ORM\Column(name="uploaded_at",type="datetime", nullable=true) 
   */
  private $uploaded_at;
  
  /**
   * Datetime when picture was taken (shot)
   * @var datetime $taken_at 
   * @ORM\Column(name="taken_at",type="datetime", nullable=true)
   */
  private $taken_at;
  

  /**
   * Related Album
   * @ORM\ManyToOne(targetEntity="Album", inversedBy="Images") 
   * @ORM\JoinColumn(name="album_id", referencedColumnName="id", onDelete="cascade")
   */
  private $Album;

  /**
   * Is private flag
   * @var boolean $is_private
   * @ORM\Column(name="is_private", type="boolean")
   */
  private $is_private = false;

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
   * Set filename
   *
   * @param string $filename
   */
  public function setFilename($filename) {
    $this->filename = $filename;
  }

  /**
   * Get filename
   *
   * @return string 
   */
  public function getFilename() {
    return $this->filename;
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
   * Gets uploaded at
   * @return datetime 
   */
  public function getUploadedAt() {
    return $this->uploaded_at;
  }
  
  /**
   * Sets uploaded at
   * @param datetime $uploaded_at 
   */
  public function setUploadedAt($uploaded_at)
  {
    $this->uploaded_at = $uploaded_at;
  }
  
  /**
   * Gets taken at
   * @return datetime 
   */
  public function getTakenAt() {
    return $this->taken_at;
  }
  
  /**
   * Sets taken at
   * @param datetime $taken_at
   */
  public function setTakenAt($taken_at)
  {
    $this->taken_at = $taken_at;
  }

}