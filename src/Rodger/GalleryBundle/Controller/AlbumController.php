<?php

namespace Rodger\GalleryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Rodger\GalleryBundle\Form\AlbumType;

/**
 * @Route("/album")
 */
class AlbumController extends CommonController {

  /**
   * @Route("/create", name="albums.create")
   * @Template()
   */
  public function createAction() {
    $album = new \Rodger\GalleryBundle\Entity\Album();
    $album->setUser($this->user);
    
    $type = new AlbumType();
    $type->setKeywordsAutocompleteSource($this->generateUrl('keywords.autocomplete'));
    $form = $this->createForm($type, $album);
    
    if ($this->getRequest()->getMethod() == 'POST') {
      $form->bindRequest($this->getRequest());
      if ($form->isValid()) {
        $tags_repository = $this->em->getRepository('RodgerGalleryBundle:Tag');
        $album = $form->getData();
        
        if ($album->keywords) {
          $keywords = explode(',', $album->keywords);
          $keywords = array_map('trim', $keywords);
          foreach($keywords as $keyword) {
            $album ->addTag($tags_repository->getOrCreate($keyword));
          }
        }
        
        $this->em->persist($album);
        $this->em->flush();
        return $this->redirect($this->generateUrl('homepage'));
      }
    }
    
    return array('form' => $form->createView());
  }

}
