<?php

namespace Rodger\GalleryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Rodger\GalleryBundle\Entity\ImageExif
 *
 * @ORM\Table(name="image__exif")
 * @ORM\Entity
 */
class ImageExif {

  /**
   * @var integer $id
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @var array $exif_data
   * @ORM\Column(name="exif_data", type="array")
   */
  private $exif_data;

  /**
   * @var array $iptc_data
   * @ORM\Column(name="iptc_data", type="array")
   */
  private $iptc_data;

  /**
   * @ORM\OneToOne(targetEntity="Image", inversedBy="Exifs")
   * @ORM\JoinColumn(name="image_id", referencedColumnName="id", onDelete="cascade")
   * @var Image $Image 
   */
  private $Image;

  /**
   * Set image
   *
   * @param Image $image
   */
  public function setImage($image) {
    $this->Image = $image;
  }

  /**
   * Get image
   *
   * @return Image 
   */
  public function getImage() {
    return $this->Image;
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

}