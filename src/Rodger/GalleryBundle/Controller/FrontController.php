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
    if (!$this->get_selected_year()) {
      $this->set_selected_year(max($this->em->getRepository('RodgerGalleryBundle:Image')->getYears($this->user)));
    }
    
    $albums = $this->em->getRepository('RodgerGalleryBundle:Album')->getLatestQueryBuilder($this->user, $this->get_filters())
            ->getQuery()->execute();
    
    return array('albums' => $albums);
  }
  
  /**
   * @Route("/album/{slug}", name="album.show")
   * @Template("RodgerGalleryBundle:Front:album_content.html.twig")
   */
  public function albumContentAction(Album $album) {
    return array('album' => $album, 
        'images' => $this->em
            ->getRepository("RodgerGalleryBundle:Image")
            ->getFilteredAlbumImages($album, $this->get_filters(), $this->user));
  }
  
  /**
   * Renders album last images
   * @Template("RodgerGalleryBundle:Front:_list_album_images.html.twig")
   * @param Album $album
   * @return array 
   */
  public function album_last_imagesAction(Album $album) {
    $this->preExecute();
    $images = $this->em->getRepository('RodgerGalleryBundle:Image')->getLatestInAlbumQueryBuilder($album, (bool)$this->user, $this->get_filters())
            ->setMaxResults(15)
            ->getQuery()->execute();
    if (!count($images) && ($this->get_selected_year() || count($this->get_filter_tags()))) {
      $images = $this->em->getRepository('RodgerGalleryBundle:Image')->getLatestInAlbumQueryBuilder($album, (bool)$this->user)
            ->setMaxResults(15)
            ->getQuery()->execute();
    }
    return array('album' => $album, 'images' => $images);
  }
  
  /**
   * Renders years menu
   * @Template("RodgerGalleryBundle:Front:_years_menu.html.twig")
   */
  public function yearsMenuAction() {
    $this->preExecute();
    $years = $this->em->getRepository('RodgerGalleryBundle:Image')->getYears($this->user);
    return array('years' => $years, 'selected' => $this->get_selected_year());
  }
  
  /**
   * Filters years
   * @Route("/year/{year}", name="filter.year", requirements={"year"="20\d{2}"})
   * @param type $year 
   */
  public function filterYearAction($year) {
    $this->set_selected_year($year);
    return $this->redirect($this->generateUrl('albums'));
  }
  
}
