<?php
namespace Rodger\GalleryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Rodger\GalleryBundle\Entity\Tag,
    Rodger\GalleryBundle\Entity\Album;

/**
 * @Route("/tags")
 */
class TagController extends CommonController {
  
  /**
   * @Template("RodgerGalleryBundle:Tag:pane.html.twig")
   */
  public function albumsListAction($filters) {
    $this->preExecute();
    return array(
        'filtered_tags' => $this->get_filter_tags(), 
        'tags' => $this->em->getRepository('RodgerGalleryBundle:Tag')->getFilteredAlbumsTags($this->user, $this->get_selected_year())
    );
  }
  
  /**
   * Renders album images tags
   * @Template("RodgerGalleryBundle:Tag:pane.html.twig")
   * @param Album $album
   * @return array
   */
  public function albumImagesAction(Album $album) {
    $this->preExecute();
    $tags = $this->em->getRepository('RodgerGalleryBundle:Tag')->getFilteredAlbumImagesTags(
            $album, 
            $this->user, 
            $this->get_filters());
    return array(
        'filtered_tags' => $this->get_filter_tags(), 
        'tags' => $tags
    );
  }
  
  /**
   * Clears filters storage
   * @Route("/_clear", name="filter.clear")
   */
  public function clearAction() {
    $this->set_filter_tags(array());
    return $this->redirect($this->getRequest()->headers->get('referer'));
  }
  
  /**
   * Adds or Removes tag from the filters storage
   * @param Tag $tag 
   * @Route("/{name}", name="filter.use")
   */
  public function processTagAction(Tag $tag)
  {
    $this->add_remove_filter_tag($tag);
    return $this->redirect($this->getRequest()->headers->get('referer'));
  }
  
  private function add_remove_filter_tag(Tag $tag)
  {
    $tags = $this->get_filter_tags();
    in_array((string)$tag, $tags) 
            ? $tags = array_diff($tags, array((string)$tag))
            : $tags[] = (string)$tag;
    $this->set_filter_tags($tags);
  }
  
}
?>
