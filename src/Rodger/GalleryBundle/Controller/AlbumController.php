<?php

namespace Rodger\GalleryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Rodger\GalleryBundle\Form\AlbumType;
use Rodger\GalleryBundle\Form\AlbumImagesType;
use Rodger\GalleryBundle\Entity\Album;
use Rodger\GalleryBundle\Entity\Image;
use Rodger\GalleryBundle\Uploader\Uploader;

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
    
    return array('form' => $form->createView() );
  }
  
  /**
   * @Route("/edit/{slug}", name="albums.edit")
   * @Template()
   * @Secure(roles="ROLE_USER")
   */
  public function editAction(Album $album) {
    $form = $this->process_album($album);
    if ($form instanceof \Symfony\Component\HttpFoundation\RedirectResponse) return $form;
    
    $images_type = new AlbumImagesType();
    $images_type->setImages($album->getImages());
    
    $images_form = $this->createForm($images_type);
    return array('form' => $form->createView(), 'album' => $album, 'images_form' => $images_form->createView());
  }
  
  private function process_album(Album $album) {
    $type = new AlbumType();
    $type->setKeywordsAutocompleteSource($this->generateUrl('keywords.autocomplete'));
    $form = $this->createForm($type, $album);
    
    if ($this->getRequest()->getMethod() == 'POST') {
      $form->bindRequest($this->getRequest());
      if ($form->isValid()) {
        
        $this->em->beginTransaction();
        $tags_repository = $this->em->getRepository('RodgerGalleryBundle:Tag');
        $album = $form->getData();
        
        $this->em->persist($album);
        $this->em->flush();
        
        // process uploads
        if ($album->upload['file']) {
          $uploader = new Uploader($album->upload['file'], $this->get('validator'), $this->em);
          $uploader->addImagesToAlbum($album, $album->upload, $this->user);
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
        
        $this->em->commit();
        
        return $this->redirect($this->generateUrl('albums.edit', array('slug' => $album->getSlug())));
      }
    }
    
    return $form;
  }
  
  /**
   * @Route("edit/{slug}/bulk-actions", name="album.edit.bulk", requirements={"_method" = "post"})
   * @Secure(roles="ROLE_USER")
   */
  public function imagesBulkAction(Album $album) {
    $images_type = new AlbumImagesType();
    $images_type->setImages($album->getImages());
    
    $images_form = $this->createForm($images_type);
    $images_form->bindRequest($this->getRequest());
    
    if ($images_form->isValid()) {
      $datas = $this->getRequest()->get($images_form->getName());
      switch($datas['bulk_actions']):
        case AlbumImagesType::ACTION_DELETE;
          foreach($this->em->getRepository('RodgerGalleryBundle:Image')->findById($datas['images']) as $image) {
            $this->em->remove($image);
          }
          $this->em->flush();
          break;
      endswitch;
    }
    
    return $this->redirect($this->generateUrl('albums.edit', array('slug' => $album->getSlug())));
  }

}
