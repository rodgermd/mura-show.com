<?php

namespace Rodger\GalleryBundle\Entity;

interface UploadableInterface {

  public function getAbsolutePath();

  public function getUploadRootDir();

  public function getUploadDir();
}

?>
