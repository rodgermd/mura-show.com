<?php
namespace Rodger\GalleryBundle\Uploader;
use Symfony\Component\HttpFoundation\File\UploadedFile,
     Symfony\Component\Validator\Validator,
     Symfony\Component\Validator\ConstraintViolationList,
     Doctrine\ORM\EntityManager,
     Rodger\GalleryBundle\Entity as Entities,
     FOS\UserBundle\Model\UserInterface;

use Rodger\GalleryBundle\Exif as ExifParsers;
use Rodger\GalleryBundle\Convert\Converter;

class Uploader {
  protected $file, $validator, $folder, $em;
  private $prototype_image;
  public $images = array();
  public $items_total = 0;
  
  public function __construct(UploadedFile $file, Validator $validator, EntityManager $em )
  {
    $this->prototype_image = new PrototypeImage();
    $this->file = $file;
    $this->validator = $validator;
    $this->em = $em;
    $this->process();
  }

  protected function process() {
    if (preg_match("#^image#", $this->file->getMimeType()))
    {
      $this->add_image($this->file->getPathname(), $this->file->getClientOriginalName());
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
  
  protected function add_image($filename, $original_filename = null)
  {
    $this->items_total++;
    $this->prototype_image->file = $filename;
    $result = $this->validator->validate($this->prototype_image);
    if(!$result->count()) {
      $this->images[$filename] = $original_filename;
    }
  }
  
  /**
   * Gets total files processed
   * @return integer 
   */
  public function getTotalFiles() { return $this->items_total; }
  
  /**
   * Gets validate files
   * @return array 
   */
  public function getImages() { return $this->images; }
  
  /**
   * Gets upload folder
   * @return string 
   */
  public function getUploadedFolder() { return $this->folder; }
  
  /**
   * Adds parsed images to Album
   * @param Entities\Album $album 
   * @param array $options
   * @param UserInterface $user
   */
  public function addImagesToAlbum(Entities\Album $album, array $options = array(), UserInterface $user = null) {
    foreach ($this->getImages() as $image_file => $name) $this->addImageToAlbum($image_file, $name, $album, $options, $user);
    if ($this->getUploadedFolder()) exec(sprintf('rm -rf %s', $this->getUploadedFolder()));
  }
  
  /**
   * Adds image to Album
   * @param string $image_file
   * @param string $default_title
   * @param Entity\Album $album 
   * @param array $options
   * @param UserInterface $user
   */
  private function addImageToAlbum($image_file, $default_title, Entities\Album $album, array $options = array(), UserInterface $user = null) {
    $image = new Entities\Image();
    $image->setFilename(sprintf("%s.%s", md5(uniqid()), strtolower(pathinfo($default_title ?: $image_file, PATHINFO_EXTENSION))));
    
    $name = pathinfo($image_file, PATHINFO_FILENAME);
    if ($default_title) $name = $default_title; 
    if (@$options['default_name']) $name = $options['default_name'];
    
    $image->setName($name);

    copy($image_file, $image->getAbsolutePath());
    $image->setUser($user);

    $exif = new ExifParsers\ExifDataParser(read_exif_data($image->getAbsolutePath()));
    $exif_parsed = $exif->getParsed();
    if (isset($exif_parsed['DateTimeOriginal'])) {
      $image->setTakenAt(new \DateTime($exif_parsed['DateTimeOriginal']));
      $image->addTag($this->tag($image->getTakenAt()->format('Y')));
    }
    
    Converter::exif_rotate($image->getAbsolutePath(), @$exif_parsed['IFD0']['Orientation']);
    
    $image_exif = new Entities\ImageExif();
    $image_exif->setExifData($exif_parsed);
    $image->setExifs($image_exif);
    
    $iptc = new ExifParsers\IptcDataParser($image->getAbsolutePath());
    $image_exif->setIptcData($iptc->getRaw());
    
    if (@$options['keywords']) {
      $keywords = array_filter(array_map('trim', explode(',', $options['keywords'])));
      foreach($keywords as $keyword) {
        $tag = $this->tag($keyword);
        $image->addTag($tag);
      }
    }
    
    if (@$options['is_private']) $image->setIsPrivate(true);
    
    $image->setAlbum($album);
    
    $this->em->persist($image);
    $this->em->persist($image_exif);
  }
  
  /**
   * Gets existing or new tag
   * @param string $name
   * @return Entity\Tag 
   */
  private function tag($name) {
    return $this->em->getRepository('RodgerGalleryBundle:Tag')->getOrCreate($name);
  }
  
}
?>
