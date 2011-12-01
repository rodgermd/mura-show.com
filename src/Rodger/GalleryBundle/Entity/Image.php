<?php

namespace Rodger\GalleryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation\Timestampable;
use Rodger\GalleryBundle\Entity\Album;
use Rodger\GalleryBundle\Entity\Tag;
use Rodger\UserBundle\Entity\User;

/**
 * Rodger\GalleryBundle\Entity\Image
 *
 * @ORM\Table(name="images")
 * @ORM\Entity(repositoryClass="Rodger\GalleryBundle\Entity\ImageRepository")
 */
class Image implements UploadableInterface {

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
   * @ORM\JoinColumn(name="album_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $Album;

  /**
   * Album id
   * @var integer $album_id
   * @ORM\Column(name="album_id", type="integer") 
   */
  private $album_id;

  /**
   * Related Tags
   * @var array Tags
   * @ORM\ManyToMany(targetEntity="Tag", inversedBy="Images")
   * @ORM\JoinTable(name="image_tags",
   *      joinColumns={@ORM\JoinColumn(name="image_id", referencedColumnName="id", onDelete="CASCADE")},
   *      inverseJoinColumns={@ORM\JoinColumn(name="tag", referencedColumnName="name", onDelete="CASCADE")}
   *      ) 
   */
  private $Tags;

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
   * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="set null")
   */
  private $User;

  /**
   * @ORM\Column(name="user_id", type="integer", nullable=true)
   * @var integer $user_id
   */
  private $user_id;
  
  /**
   * @var ImageExif $Exifs
   * @ORM\OneToOne(targetEntity="ImageExif", mappedBy="Image")
   */
  private $Exifs;
  
  public function __construct() {
    $this->Tags = new ArrayCollection();
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
  public function setUploadedAt($uploaded_at) {
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
  public function setTakenAt($taken_at) {
    $this->taken_at = $taken_at;
  }

  /**
   * Sets related album
   * @param Album $album 
   */
  public function setAlbum(Album $album) {
    $this->Album = $album;
    $this->album_id = $album->getId();
  }

  /**
   * Gets related Album
   * @return Album 
   */
  public function getAlbum() {
    return $this->Album;
  }

  /**
   * Sets related Tags
   * @param array $tags 
   */
  public function setTags($tags) {
    $this->Tags = $tags;
  }

  /**
   * Adds a Tag into collection
   * @param Tag $tag 
   */
  public function addTag(Tag $tag) {
    $this->Tags[] = $tag;
  }

  /**
   * Gets related Tags
   * @return array 
   */
  public function getTags() {
    return $this->Tags;
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
   * Sets Exifs
   * @param ImageExif $image_exif 
   */
  public function setExifs(ImageExif $image_exif) {
    $this->Exifs = $image_exif;
  }
  
  /**
   * Gets related Exifs
   * @return ImageExif
   */
  public function getExifs() {
    return $this->Exifs;
  }

  public function __toString() {
    return $this->name;
  }
  
  /* uploadable interface implementation */
  
  
  public function getAbsolutePath() {
    return null === $this->filename ? null : $this->getUploadRootDir() . '/' . $this->filename;
  }

  public function getWebPath() {
    return null === $this->filename ? null : $this->getUploadDir() . '/' . $this->filename;
  }

  public function getUploadRootDir() {
    // the absolute directory path where uploaded documents should be saved
    return __DIR__ . '/../../../../' . $this->getUploadDir();
  }

  public function getUploadDir() {
    // get rid of the __DIR__ so it doesn't screw when displaying uploaded doc/image in the view.
    return 'uploads/images';
  }
}