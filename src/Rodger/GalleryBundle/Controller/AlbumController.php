<?php

namespace Rodger\GalleryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Rodger\GalleryBundle\Form as Forms,
    Rodger\GalleryBundle\Entity\Album,
    Rodger\GalleryBundle\Entity\Image,
    Rodger\GalleryBundle\Uploader\Uploader;

use Rodger\GalleryBundle\ValidateHelper as ValidateHelpers;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;

/**
 * @Route("/album")
 */
class AlbumController extends CommonController {
protected $bulk_form, $validating_object, $paginator;
  /**
   * @Route("/create", name="albums.create")
   * @Template()
   * @Secure(roles="ROLE_USER")
   */
  public function createAction() {
    $album = new Album();
    $album->setUser($this->user);
    
    $form = $this->process_album($album);
    if ($form instanceof \Symfony\Component\HttpFoundation\RedirectResponse) return $form;
    
    return array('form' => $form->createView() );
  }
  
  /**
   * @Route("/edit/{slug}", name="albums.edit")
   * @Template()
   * @Secure(roles="ROLE_USER")
   */
  public function editAction(Album $album) {
    $form = $this->process_album($album);
    if ($form instanceof \Symfony\Component\HttpFoundation\RedirectResponse) return $form;

    return array('form' => $form->createView(), 'album' => $album);
  }

  /**
   * @Route("/delete/{slug}", name="albums.delete")
   * @Template()
   * @Secure(roles="ROLE_USER")
   */
  public function deleteAction(Album $album) {
    $album->delete_images();
    $this->em->remove($album);
    $this->em->flush();
    return $this->redirect($this->generateUrl('albums'));
  }
  
  private function process_album(Album $album) {
    $type = new Forms\AlbumType();
    $type->setKeywordsAutocompleteSource($this->generateUrl('keywords.autocomplete'));
    $form = $this->createForm($type, $album);
    
    if ($this->getRequest()->getMethod() == 'POST') {
      
      $old_album = clone $album;
      
      $form->bindRequest($this->getRequest());
      if ($form->isValid()) {
        
        $this->em->beginTransaction();
        $tags_repository = $this->em->getRepository('RodgerGalleryBundle:Tag');
        
        $this->em->persist($album);
        
        $this->em->flush();
        
        if ($old_album->getId() && $old_album->getSlug() != $album->getSlug()) {
          $result = rename($old_album->getUploadRootDir(), $album->getUploadRootDir());
          $result = rename($old_album->getThumbnailsFolder(true), $album->getThumbnailsFolder(true));
        }
        
        // process uploads
        if ($album->upload['file']) {
          $uploader = new Uploader($album->upload['file'], $this->get('validator'), $this->em);
          $uploader->addImagesToAlbum($album, $album->upload, $this->user);
        }
        
        // process keywords
        if ($album->keywords) {
          $keywords = explode(',', $album->keywords);
          $keywords = array_filter(array_map('trim', $keywords));
          $tags = array();
          foreach($keywords as $keyword) {
            $tags[] = $tags_repository->getOrCreate($keyword);
          }
          $album->setTags($tags);
        }
        
        $this->em->persist($album);
        $this->em->flush();
        
        $this->em->commit();
        
        return $this->redirect($this->generateUrl('albums.edit', array('slug' => $album->getSlug())));
      }
    }
    
    return $form;
  }
  
   /**
   * Renders images list
   * @param integer $page
   * @Route("/album/{slug}/list/{page}", name="admin.images.list", requirements={"page"="\d+"}, defaults={"page"=1})
   * @Template("RodgerGalleryBundle:Album:list.html.twig")
   * @Secure(roles="ROLE_USER")
   */
  public function adminListAction(Album $album, $page = 1)
  {
    $this->preExecute();
    $this->list_common_procedures($album, $page);
    
    return array('form' => $this->bulk_form->createView(), 
                 'paginator' => $this->paginator, 
                 'form_name' => $this->bulk_form->getName(),
                 'album' => $album,
                 'nocache' => uniqid()
        
        );
  }
  
  /**
   * Processes bulk actions
   * @Route("/bulk-actions/{slug}/{page}", name="admin.images.bulk", requirements={"page"="\d+", "_method"="post"})
   * @Template("RodgerGalleryBundle:Album:list.html.twig")
   * @Secure(roles="ROLE_USER")
   */
  public function bulkImagesAction(Album $album, $page) {
    $this->list_common_procedures($album, $page);

    $this->bulk_form->bindRequest($this->getRequest());
    if ($this->bulk_form->isValid()) {
      $this->bulk_form->getData()->process();
      $this->em->flush();
      $number_processed = $this->bulk_form->getData()->images->count();
      $this->session->setFlash('success', $this->get('translator')->transChoice(
         '{1}One image was %action%|]1,Inf]%number% images was %action%' ,
         $number_processed,
         array('%number%' => $number_processed,
               '%action%' => $this->get('translator')->trans(sprintf('bulk_action_past_%s', $this->bulk_form->getData()->action))
              )));

      return $this->redirect($this->getRequest()->headers->get('referer'), $this->getRequest()->isXmlHttpRequest() ? 201 : 302);
    }
    else return array(
        'form'      => $this->bulk_form->createView(), 
        'paginator' => $this->paginator, 
        'form_name' => $this->bulk_form->getName(),
        'album'     => $album,
        );
  }
  
  protected function list_common_procedures(Album $album, $page) {
    $query_builder = $this->em->getRepository('RodgerGalleryBundle:Image')
            ->createQueryBuilder('i')
            ->where('i.album_id = :album_id')
            ->orderBy('i.uploaded_at', 'desc')
            ->setParameter('album_id', $album->getId());

    $adapter = new DoctrineORMAdapter($query_builder);

    $this->paginator = new Pagerfanta($adapter);
    $this->paginator->setMaxPerPage(20);
    $this->paginator->setCurrentPage($page);
    $this->paginator->getNbPages();
    $ids = array();
    foreach($this->paginator->getCurrentPageResults() as $img) $ids[] = $img->getId();

    $this->validating_object = new ValidateHelpers\BulkImages(
            $query_builder->andWhere($query_builder->expr()->in('i.id', count($ids) ? $ids : array(0))),
            $this->em
            );
    $this->bulk_form = $this->createForm(new Forms\BulkImages(), $this->validating_object);
  }


}
