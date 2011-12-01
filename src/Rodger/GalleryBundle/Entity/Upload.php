<?php
namespace Rodger\GalleryBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class Upload {
  
  /**
   * Default name
   * @var string $default_name
   */
  public $default_name;
  
  /**
   * Is the file (files) private
   * @var boolean $is_private
   */
  public $is_private;
  
  /**
   * Keywords, delimited by comma
   * @var string $keywords
   */
  public $keywords;
  
  /**
   * Uploading file
   * @var mixed $file
   * @Assert\File(
   *     mimeTypes = {"application/zip", "image/jpg"},
   *     mimeTypesMessage = "Please upload a ZIP or JPG file"
   * )
   */
  public $file;
}
?>
