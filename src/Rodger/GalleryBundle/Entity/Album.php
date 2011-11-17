<?php

namespace Rodger\GalleryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation\Timestampable;
use Rodger\UserBundle\Entity\User;
use Rodger\GalleryBundle\Entity\Image;

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
  
  /**
   * Related User
   * @var User $user 
   * @ORM\ManyToOne(targetEntity="Rodger\UserBundle\Entity\User", inversedBy="Images") 
   * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="cascade")
   */
  private $User;

  /**
   * @ORM\Column(name="user_id", type="integer")
   * @var integer $user_id
   */
  private $user_id;

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
  
  /**
   * Gets uploader User
   * @return User 
   */
  public function getUser() {
    return $this->User;
  }
  
  /**
   * Sets uploader User
   * @param User $user 
   */
  public function setUser(User $user)
  {
    $this->User = $user;
    $this->user_id = $user->getId();
  }
  
  /**
   * Gets related Images
   * @return array 
   */
  public function getImages() {
    return $this->Images;
  }
  
  /**
   * Adds Image
   * @param Image $image 
   */
  public function addImage(Image $image)
  {
    $this->Images[] = $image;
  }

}