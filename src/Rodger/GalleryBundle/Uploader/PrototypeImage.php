<?php
namespace Rodger\GalleryBundle\Uploader;
use Symfony\Component\Validator\Constraints as Assert;

class PrototypeImage 
{
  /**
   *
   * @var mixed $file
   * @Assert\File(
   *     mimeTypes = {"image/jpeg"},
   *     mimeTypesMessage = "not a valid JPG file"
   * )
   */
  public $file;
  
  public function getFilename() {
    return pathinfo($this->file, PATHINFO_FILENAME);
  }
}
?>
