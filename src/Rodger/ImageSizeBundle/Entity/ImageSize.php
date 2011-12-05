<?php

namespace Rodger\ImageSizeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Rodger\ImageSizeBundle\Entity\ImageSize
 *
 * @ORM\Table(name="image__size")
 * @ORM\Entity
 */
class ImageSize {
  const FITMODE_FIT = false;
  const FITMODE_CROP = true;

  /**
   * @var string $name
   * @ORM\Id
   * @ORM\Column(name="name", type="string", length=50)
   */
  private $name;

  /**
   * @var integer $width
   *
   * @ORM\Column(name="width", type="integer")
   */
  private $width;

  /**
   * @var integer $height
   *
   * @ORM\Column(name="height", type="integer", nullable=true)
   */
  private $height;

  /**
   * @var boolean $crop
   *
   * @ORM\Column(name="crop", type="boolean")
   */
  private $crop = false;

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
   * Set width
   *
   * @param integer $width
   */
  public function setWidth($width) {
    $this->width = $width;
  }

  /**
   * Get width
   *
   * @return integer 
   */
  public function getWidth() {
    return $this->width;
  }

  /**
   * Set height
   *
   * @param integer $height
   */
  public function setHeight($height) {
    $this->height = $height;
  }

  /**
   * Get height
   *
   * @return integer 
   */
  public function getHeight() {
    return $this->height;
  }

  /**
   * Set crop
   *
   * @param boolean $crop
   */
  public function setCrop($crop) {
    $this->crop = $crop;
  }

  /**
   * Get crop
   *
   * @return boolean 
   */
  public function getCrop() {
    return $this->crop;
  }
  
}