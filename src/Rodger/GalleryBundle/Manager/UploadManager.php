<?php
/**
 * Created by JetBrains PhpStorm.
 * User: rodger
 * Date: 25.03.13
 * Time: 22:20
 * To change this template use File | Settings | File Templates.
 */

namespace Rodger\GalleryBundle\Manager;


use Doctrine\ORM\EntityManager;
use Liip\ImagineBundle\Templating\Helper\ImagineHelper;
use Rodger\GalleryBundle\Entity\Image;
use Rodger\GalleryBundle\Exif\ExifDataParser;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Router;
use Vich\UploaderBundle\Storage\FileSystemStorage;
use Vich\UploaderBundle\Storage\GaufretteStorage;
use Vich\UploaderBundle\Twig\Extension\UploaderExtension;

class UploadManager
{
  /** @var EntityManager $em */
  protected $em;
  /** @var Router $router */
  protected $router;
  /** @var UploaderExtension $vich_uploader */
  protected $vich_uploader;
  /** @var FileSystemStorage */
  protected $storage;
  /** @var ImagineHelper $liip_helper */
  protected $liip_helper;

  public function __construct(Container $container)
  {
    $this->em            = $container->get('doctrine')->getManager();
    $this->router        = $container->get('router');
    $this->vich_uploader = $container->get('vich_uploader.templating.helper.uploader_helper');
    $this->liip_helper   = $container->get('liip_imagine.templating.helper');
    $this->storage       = $container->get('vich_uploader.storage.file_system');
  }

  public function save(Image $image)
  {
    $this->em->persist($image);
    $this->vich_uploader->asset($image, 'file');
    $uploaded_file = $image->getFile();

    $this->update_exif($image);
    $this->em->flush();

    $filepath = $this->getFilepath($image);

    $response = array(
      'files' =>
      array(
        array(
          'name'          => $uploaded_file->getClientOriginalName(),
          'size'          => filesize($filepath),
          'url'           => $this->router->generate('image.show', array('album' => $image->getAlbum()->getSlug(), 'id' => $image->getId())),
          'thumbnail_url' => $this->liip_helper->filter($this->vich_uploader->asset($image, 'file'), 'list'),
          'delete_url'    => $this->router->generate('image.delete', array('id' => $image->getId())),
          'delete_type'   => 'POST'
        )));

    return new Response(json_encode($response));
  }

  protected function update_exif(Image $image)
  {
    $exif        = new ExifDataParser(@read_exif_data($this->getFilepath($image)));
    $exif_parsed = $exif->getParsed();
    $image->setExifData($exif_parsed);

    $datetime = null;
    try {
      $datetime = new \DateTime(@$exif_parsed['DateTimeOriginal']);
    }
    catch (\Exception $e) {
      $datetime = new \DateTime($exif_parsed['DateTime']);
    }

    $image->setTakenAt($datetime);
    $this->em->persist($image);

  }

  /**
   * Gets filepath
   * @param Image $image
   * @return string
   */
  protected function getFilepath(Image $image)
  {
    return $this->storage->resolvePath($image, 'file');
  }

}