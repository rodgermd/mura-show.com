<?php

namespace Rodger\GalleryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Route("/album")
 */
class AlbumController extends CommonController {

  /**
   * @Route("/create", name="albums.create")
   * @Template()
   */
  public function createAction() {
    return array();
  }

}
