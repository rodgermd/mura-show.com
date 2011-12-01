<?php

namespace Rodger\GalleryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Rodger\GalleryBundle\Form\AlbumType;
use Rodger\GalleryBundle\Entity\Album;
use Rodger\GalleryBundle\Entity\Image;
use Rodger\GalleryBundle\Uploader\Uploader;

use Rodger\GalleryBundle\Exif\ExifDataParser;
use Rodger\GalleryBundle\Convert\Converter;

/**
 * @Route("/album")
 */
class AlbumController extends CommonController {

  /**
   * @Route("/create", name="albums.create")
   * @Template()
   * @Secure(roles="ROLE_USER")
   */
  public function createAction() {
    $album = new Album();
    $album->setUser($this->user);
    
    $form = $this->process_album($album);
    if ($form instanceof \Symfony\Component\HttpFoundation\RedirectResponse) return $form;
    
    return array('form' => $form->createView());
  }
  
  /**
   * @Route("/edit/{slug}", name="albums.edit")
   * @Template()
   * @Secure(roles="ROLE_USER")
   */
  public function editAction(Album $album) {
    $form = $this->process_album($album);
    if ($form instanceof \Symfony\Component\HttpFoundation\RedirectResponse) return $form;
    
    return array('form' => $form->createView(), 'album' => $album);
  }
  
  private function process_album(Album $album) {
    $type = new AlbumType();
    $type->setKeywordsAutocompleteSource($this->generateUrl('keywords.autocomplete'));
    $form = $this->createForm($type, $album);
    
    if ($this->getRequest()->getMethod() == 'POST') {
      $form->bindRequest($this->getRequest());
      if ($form->isValid()) {
        $tags_repository = $this->em->getRepository('RodgerGalleryBundle:Tag');
        $album = $form->getData();
        
        // process uploads
        if ($album->file) {
          $uploader = new Uploader($album->file, $this->get('validator'));
          foreach($uploader->getImages() as $image_file) {
            $image = new Image();
            $image->setFilename(sprintf("%s.%s", md5(uniqid()), strtolower(pathinfo($image_file, PATHINFO_EXTENSION))));
            $image->setName(pathinfo($image_file, PATHINFO_FILENAME));
            
            copy($image_file, $image->getAbsolutePath());
            $image->setAlbum($album);
            $image->setUser($this->user);
            
            $exif = new ExifDataParser(array('EXIF' => read_exif_data($image->getAbsolutePath())));
            $exif_parsed = $exif->getParsed();
            if (isset($exif_parsed['DateTimeOriginal'])) {
              $image->setTakenAt(new \DateTime($exif_parsed['DateTimeOriginal']));
            }
            
            $this->em->persist($image);
          }
          exec(sprintf('rm -rf %s', $uploader->getUploadedFolder()));
        }
        // process keywords
        if ($album->keywords) {
          $keywords = explode(',', $album->keywords);
          $keywords = array_filter(array_map('trim', $keywords))  ;
          foreach($keywords as $keyword) {
            $tag = $tags_repository->getOrCreate($keyword);
            $album->addTag($tag);
          }
        }
        
        $this->em->persist($album);
        $this->em->flush();
        return $this->redirect($this->generateUrl('albums.edit', array('slug' => $album->getSlug())));
      }
    }
    
    return $form;
  }

}
