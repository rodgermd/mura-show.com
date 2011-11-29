<?php
namespace Rodger\GalleryBundle\Uploader;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Validator;
use Symfony\Component\Validator\ConstraintViolationList;

class Uploader {
  protected $file, $validator, $folder;
  private $prototype_image;
  public $images = array();
  public $items_total = 0;
  
  public function __construct(UploadedFile $file, Validator $validator)
  {
    $this->prototype_image = new PrototypeImage();
    $this->file = $file;
    $this->validator = $validator;
    $this->process();
  }

  protected function process() {
    if (preg_match("#^image#", $this->file->getMimeType()))
    {
      $this->images[] = $this->file;
      $this->items_total = 1; 
    }
    else {
      $this->process_archive();
    }
  }
  
  protected function process_archive()
  {
    $zip = new \ZipArchive();
    $res = $zip->open($this->file->getPathname());
    
    if ($res === TRUE) {
      $this->folder = $this->file->getPath() . '/' . uniqid();
      $zip->extractTo($this->folder);
      chmod($this->folder, 0777);
      $dir = opendir($this->folder);
      while($filename = readdir($dir))
      {
        if ($filename == "." || $filename == ".." || !is_file($this->folder . '/' . $filename)) continue;
        $this->add_image($this->folder . '/' . $filename);
      }
    }
    
  }
  
  protected function add_image($filename)
  {
    $this->items_total++;
    $this->prototype_image->file = $filename;
    $result = $this->validator->validate($this->prototype_image);
    if(!$result->count()) {
      $this->images[] = $filename;
    }
  }
  
  public function getTotalFiles() { return $this->items_total; }
  
  public function getImages() { return $this->images; }
  
  public function getUploadedFolder() { return $this->folder; }
  
}
?>
