<?php

namespace Rodger\GalleryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class CommonController extends Controller
{
  public $user, $em, $session;

  public function preExecute()
  {
    $token = $this->get('security.context')->getToken();
    $this->user = ($token && is_object($token->getUser())) ? $token->getUser() : null;
    $this->em = $this->getDoctrine()->getEntityManager();
    $this->session = $this->get('session');
  }
  
  /**
   * @Route("/keywords-autocomplete", name="keywords.autocomplete", requirements={"_method"="get"})
   */
  public function keywordsAutocompleteAction() {
    $search_string = $this->getRequest()->get('search_string');
    if (!$search_string) throw $this->createNotFoundException();

    $keywords = $this->em->getRepository('RodgerGalleryBundle:Tag')->search($search_string);
    $result = array();
    foreach($keywords as $keyword) $result[] = array('label' => (string) $keyword);
    $response = $this->getJsonResponse();
    $response->setContent(json_encode($result));

    return $response;
  }
  
  public function getJsonResponse() {
    $response = new Response();
    $response->headers->set('Content-Type', 'application/json; charset=UTF-8');
    return $response;
  }
  
}
