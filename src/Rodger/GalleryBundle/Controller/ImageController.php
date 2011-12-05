<?php
namespace Rodger\GalleryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Rodger\GalleryBundle\Entity\Album;
use Rodger\GalleryBundle\Entity\Image;

use Rodger\GalleryBundle\Form\ImageType;

/**
 * @Route("/image")
 */
class ImageController extends CommonController {
  
  /**
   * @Route("/edit/{id}", name="image.edit", requirements={"id"="\d+"})
   * @Secure(roles="ROLE_USER")
   * @Template
   * @param Image $image 
   */
  public function editAction(Image $image) {
    $type = new ImageType();
    $type->setKeywordsAutocompleteSource($this->generateUrl('keywords.autocomplete'));
    $form = $this->createForm($type, $image);

    return array('form' => $form->createView(), 'image' => $image);
  }
  
  /**
   * @Route("/update/{id}", name="image.update", requirements={"id"="\d+", "_method"="post"})
   * @Secure(roles="ROLE_USER")
   * @Template("RodgerGalleryBundle:Image:edit.html.twig")
   * @param Image $image 
   */
  public function updateAction(Image $image) {
    $type = new ImageType();
    $type->setKeywordsAutocompleteSource($this->generateUrl('keywords.autocomplete'));
    $form = $this->createForm($type, $image);
    
    $form->bindRequest($this->getRequest());
    
    if ($form->isValid()) {
      $keywords = array_filter(array_map('trim', explode(",", $image->keywords)));
      $tags = array();
      if (count($keywords)) {
        foreach($keywords as $keyword) {
           $tags []= $this->em->getRepository('RodgerGalleryBundle:Tag')->getOrCreate($keyword); 
        }
      }
      
      $image->setTags($tags);
      
      $this->em->persist($image);
      $this->em->flush();
      
      return $this->redirect($this->generateUrl('image.edit', array('id' => $image->getId())));
    }

    return array('form' => $form->createView(), 'image' => $image);
  }
}
?>
