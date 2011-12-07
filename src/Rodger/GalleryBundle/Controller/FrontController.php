<?php

namespace Rodger\GalleryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Rodger\GalleryBundle\Entity\Album,
    Rodger\GalleryBundle\Entity\Image;

/**
 * Route("/")
 */
class FrontController extends CommonController {

  /**
   * @Route("/", name="homepage")
   * @Route("/albums", name="albums")
   * @Template()
   */
  public function albumsAction() {
    $albums = $this->em->getRepository('RodgerGalleryBundle:Album')->getLatestQueryBuilder((bool)$this->user)
            ->getQuery()->execute();
    
    return array('albums' => $albums);
  }
  
  /**
   * @Route("{slug}", name="album.show")
   * @Template("RodgerGalleryBundle:Front:album_content.html.twig")
   */
  public function albumContentAction(Album $album) {
    return array('album' => $album, 'images' => $album->getImages());
  }
  
  /**
   * Renders album last images
   * @Template("RodgerGalleryBundle:Front:_list_album_images.html.twig")
   * @param Album $album
   * @return array 
   */
  public function album_last_imagesAction(Album $album) {
    $this->preExecute();
    $images = $this->em->getRepository('RodgerGalleryBundle:Image')->getLatestInAlbumQueryBuilder($album, (bool)$this->user)
            ->setMaxResults(15)
            ->getQuery()->execute();
    return array('album' => $album, 'images' => $images);
  }
  
  

}
