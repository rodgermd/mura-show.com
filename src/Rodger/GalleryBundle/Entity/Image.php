<?php

namespace Rodger\GalleryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation\Timestampable;
use Rodger\GalleryBundle\Entity\Album;
use Rodger\GalleryBundle\Entity\Tag;
use Rodger\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Rodger\GalleryBundle\Entity\Image
 *
 * @ORM\Table(name="images", uniqueConstraints={
 *   @ORM\UniqueConstraint(name="filename_unique",columns={"filename"})
 * },
 * indexes={@ORM\Index(name="year_month_idx", columns={"year", "month"})})
 * )
 * @ORM\Entity(repositoryClass="Rodger\GalleryBundle\Entity\ImageRepository")
 * @Vich\Uploadable
 */
class Image {

  /**
   * @var integer $id
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   * @Vich\Uploadable
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
   * @var \DateTime $uploaded_at
   * @Timestampable(on="create")
   * @ORM\Column(name="uploaded_at",type="datetime", nullable=true) 
   */
  private $uploaded_at;

  /**
   * Datetime when picture was taken (shot)
   * @var \DateTime $taken_at
   * @ORM\Column(name="taken_at",type="datetime", nullable=true)
   */
  private $taken_at;
  
  /**
   * Year of image taken date
   * @var integer $year
   * @ORM\Column(type="integer", nullable=true)
   */
  private $year;
  
  /**
   * month of image taken date
   * @var integer $month
   * @ORM\Column(type="integer", nullable=true)
   */
  private $month;

  /**
   * Related Album
   * @ORM\ManyToOne(targetEntity="Album", inversedBy="images")
   * @ORM\JoinColumn(name="album_id", referencedColumnName="id", onDelete="CASCADE")
   */
  private $album;

  /**
   * Related Tags
   * @var array Tags
   * @ORM\ManyToMany(targetEntity="Tag", inversedBy="images")
   * @ORM\JoinTable(name="image_tags",
   *      joinColumns={@ORM\JoinColumn(name="image_id", referencedColumnName="id", onDelete="CASCADE")},
   *      inverseJoinColumns={@ORM\JoinColumn(name="tag", referencedColumnName="name", onDelete="CASCADE")}
   *      ) 
   */
  private $tags;

  /**
   * Is private flag
   * @var boolean $is_private
   * @ORM\Column(name="is_private", type="boolean")
   */
  private $is_private = false;

  /**
   * Related User
   * @var User $user 
   * @ORM\ManyToOne(targetEntity="Rodger\UserBundle\Entity\User", inversedBy="images")
   * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="set null")
   */
  private $user;

  /**
   * @var array $exif_data
   * @ORM\Column(name="exif_data", type="array")
   */
  private $exif_data;

  /**
   * @Assert\File(
   *     maxSize="6M",
   *     mimeTypes={"image/png", "image/jpeg", "image/pjpeg"}
   * )
   * @Vich\UploadableField(mapping="image", fileNameProperty="filename")
   * @var UploadedFile $image
   */
  private $file;


  
  /**
   * Related keywords
   * @var string
   */
  protected $keywords;

  public function __construct() {
    $this->tags = new ArrayCollection();
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
   * @return \DateTime
   */
  public function getTakenAt() {
    return $this->taken_at;
  }

  /**
   * Sets taken at
   * @param \DateTime $taken_at
   */
  public function setTakenAt($taken_at) {
    $this->taken_at = $taken_at;
    $this->year = $taken_at->format('Y');
    $this->month = $taken_at->format('m');
  }
  
  /**
   * Sets related album
   * @param Album $album 
   */
  public function setAlbum(Album $album) {
    $this->album = $album;
  }

  /**
   * Gets related Album
   * @return Album 
   */
  public function getAlbum() {
    return $this->album;
  }

  /**
   * Sets related Tags
   * @param array $tags 
   */
  public function setTags($tags) {
    $this->tags = $tags;
  }

  /**
   * Adds a Tag into collection
   * @param Tag $tag 
   */
  public function addTag(Tag $tag) {
    $this->tags[] = $tag;
  }

  /**
   * Gets related Tags
   * @return array 
   */
  public function getTags() {
    return $this->tags;
  }
  
  /**
   * Gets uploader User
   * @return User 
   */
  public function getUser() {
    return $this->user;
  }
  
  /**
   * Sets uploader User
   * @param User $user 
   */
  public function setUser(User $user)
  {
    $this->user = $user;
  }
  
  public function __toString() {
    return $this->name;
  }
  
  /**
   * Set exif data
   *
   * @param array $exif_data
   */
  public function setExifData(array $data) {
    $this->exif_data = $data;
  }

  /**
   * Get exif data
   *
   * @return array 
   */
  public function getExifData() {
    return $this->exif_data;
  }

  /**
   * Set Iptc data
   *
   * @param array $Iptc_data
   */
  public function setIptcData(array $data) {
    $this->iptc_data = $data;
  }

  /**
   * Get Iptc data
   *
   * @return array 
   */
  public function getIptcData() {
    return $this->iptc_data;
  }

  public function getKeywords() {
    $result = array();
    foreach($this->tags as $tag) $result[] = (string)$tag;
    sort($result);
    return implode(', ', $result);
  }

  /**
   * @param string $keywords
   */
  public function setKeywords($keywords)
  {
    $this->keywords = $keywords;
  }

  /**
   * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
   */
  public function setFile(UploadedFile $file)
  {
    $this->file = $file;
    $this->name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
  }

  /**
   * @return \Symfony\Component\HttpFoundation\File\UploadedFile
   */
  public function getFile()
  {
    return $this->file;
  }
}