<?php

namespace Rodger\GalleryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Route("/gallery")
 */
class FrontController extends CommonController {

  /**
   * @Route("/", name="homepage")
   * @Template()
   */
  public function indexAction() {
    return array();
  }

}
