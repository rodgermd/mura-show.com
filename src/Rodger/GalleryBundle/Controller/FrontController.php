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
    };
    $filters = $this->get_filters();
    
    $albums = $this->em->getRepository('RodgerGalleryBundle:Album')->getLatestQueryBuilder($this->user, $filters)
            ->getQuery()->execute();
    
    $images_holder = array();
    $images_manager = $this->em->getRepository('RodgerGalleryBundle:Image');
    foreach($albums as $album) {
      $album = $album[0];
      $images = $images_manager->getLatestInAlbumQueryBuilder($album, (bool)$this->user, $filters)
            ->setMaxResults(15)
            ->getQuery()->execute();
      if (!count($images)) {
        $images = $images_manager->getLatestInAlbumQueryBuilder($album, (bool)$this->user)
            ->setMaxResults(15)
            ->getQuery()->execute();
      }
      $images_holder[$album->getSlug()] = $images;
    }
    
    return array('albums' => $albums, 'images' => $images_holder, 'filters' => $filters);
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
