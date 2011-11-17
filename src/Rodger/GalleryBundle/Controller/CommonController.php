<?php

namespace Rodger\GalleryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

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
}
