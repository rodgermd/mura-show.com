<?php

namespace Rodger\GalleryBundle\Controller;

use Rodger\GalleryBundle\Manager\UploadManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Rodger\GalleryBundle\Form as Forms,
    Rodger\GalleryBundle\Entity\Album,
    Rodger\GalleryBundle\Entity\Image;

use Rodger\GalleryBundle\ValidateHelper as ValidateHelpers;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Route("/album")
 */
class AlbumController extends CommonController
{
    protected $bulk_form, $validating_object;
    /** @var \Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination $pagination */
    protected $pagination;

    /**
     * @Route("/create", name="albums.create")
     * @Template()
     * @Secure(roles="ROLE_USER")
     */
    public function createAction()
    {
        $album = new Album();
        $album->setUser($this->user);

        $form = $this->process_album($album);
        if ($form instanceof RedirectResponse) {
            return $form;
        }

        return array('form' => $form->createView());
    }

    /**
     * @Route("/edit/{slug}", name="albums.edit")
     * @Template()
     * @Secure(roles="ROLE_USER")
     */
    public function editAction(Album $album)
    {
        $form = $this->process_album($album);
        if ($form instanceof RedirectResponse) {
            return $form;
        }
        $page = $this->getRequest()->get('page', 1);

        return array('form' => $form->createView(), 'album' => $album, 'page' => $page);
    }

    /**
     * @Route("/delete/{slug}", name="albums.delete")
     * @Template()
     * @Secure(roles="ROLE_USER")
     */
    public function deleteAction(Album $album)
    {
        $this->em->remove($album);
        $this->em->flush();

        return $this->redirect($this->generateUrl('albums'));
    }

    private function process_album(Album $album)
    {
        $type = new Forms\AlbumType();
        $type->setKeywordsAutocompleteSource($this->generateUrl('keywords.autocomplete'));
        $form = $this->createForm($type, $album);

        if ($this->getRequest()->getMethod() == 'POST') {

            $form->submit($this->getRequest());
            if ($form->isValid()) {

                $this->em->beginTransaction();
                $tags_repository = $this->em->getRepository('RodgerGalleryBundle:Tag');
                $this->em->persist($album);
                $this->em->flush();

                // process keywords
                if ($album->getKeywords()) {
                    $keywords = explode(',', $album->getKeywords());
                    $keywords = array_filter(array_map('trim', $keywords));
                    $tags     = array();
                    foreach ($keywords as $keyword) {
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
     *
     * @param integer $page
     * @Route("/album/{slug}/list", name="admin.images.list", requirements={"page"="\d+"}, defaults={"page"=1})
     * @Template("RodgerGalleryBundle:Album:list.html.twig")
     * @Secure(roles="ROLE_USER")
     */
    public function adminListAction(Album $album)
    {
        $this->preExecute();
        $this->list_common_procedures($album);

        return array(
            'form'       => $this->bulk_form->createView(),
            'pagination' => $this->pagination,
            'form_name'  => $this->bulk_form->getName(),
            'album'      => $album,
            'nocache'    => uniqid()

        );
    }

    /**
     * Processes bulk actions
     * @Route("/bulk-actions/{slug}/{page}", name="admin.images.bulk", requirements={"page"="\d+", "_method"="post"})
     * @Template("RodgerGalleryBundle:Album:list.html.twig")
     * @Secure(roles="ROLE_USER")
     */
    public function bulkImagesAction(Album $album, $page)
    {
        $this->list_common_procedures($album, $page);

        $this->bulk_form->submit($this->getRequest());
        if ($this->bulk_form->isValid()) {
            $this->bulk_form->getData()->process();
            $this->em->flush();
            $number_processed = $this->bulk_form->getData()->images->count();
            $this->session->getFlashBag()->add(
                'success',
                $this->get('translator')->transChoice(
                    '{1}One image was %action%|]1,Inf]%number% images was %action%',
                    $number_processed,
                    array(
                        '%number%' => $number_processed,
                        '%action%' => $this->get('translator')->trans(
                            sprintf('bulk_action_past_%s', $this->bulk_form->getData()->action)
                        )
                    )
                )
            );

            return $this->redirect(
                $this->getRequest()->headers->get('referer'),
                $this->getRequest()->isXmlHttpRequest() ? 201 : 302
            );
        } else {
            return array(
                'form'       => $this->bulk_form->createView(),
                'pagination' => $this->pagination,
                'form_name'  => $this->bulk_form->getName(),
                'album'      => $album,
            );
        }
    }

    protected function list_common_procedures(Album $album)
    {
        $query_builder = $this->em->getRepository('RodgerGalleryBundle:Image')
            ->createQueryBuilder('i')
            ->where('i.album = :album')
            ->orderBy('i.uploadedAt', 'desc')
            ->setParameter('album', $album->getId());

        $paginator = $this->get('knp_paginator');

        /** @var \Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination $pagination */
        $pagination = $paginator->paginate(
            $query_builder,
            $this->getRequest()->get('page', 1),
            20
        );

        $this->pagination = $pagination;

        $ids = array();
        foreach ($this->pagination->getItems() as $img) {
            $ids[] = $img->getId();
        }

        $this->validating_object = new ValidateHelpers\BulkImages(
            $query_builder->andWhere($query_builder->expr()->in('i.id', count($ids) ? $ids : array(0))),
            $this->em,
            $this->container
        );
        $this->bulk_form         = $this->createForm(new Forms\BulkImages(), $this->validating_object);
    }

    /**
     * @Route("/{slug}/upload", name="album.upload")
     * @Template
     * @Secure(roles="ROLE_USER")
     *
     * @param Album $album
     *
     * @throws AccessDeniedException
     * @return array|\Symfony\Component\HttpFoundation\Response
     */
    public function uploadAction(Album $album)
    {
        if (!$album->getUser()->equals($this->getUser())) {
            throw new AccessDeniedException();
        }

        $image = new Image();
        $image->setAlbum($album);
        $image->setUser($this->getUser());
        $form = $this->createForm(new Forms\ImageType(), $image);
        if ($this->getRequest()->isMethod('POST')) {
            $form->submit($this->getRequest());
            if ($form->isValid()) {
                /** @var UploadManager $manager */
                $manager = $this->get('gallery.upload_manager');

                return $manager->save($image);

            }

            return new Response('', 500);
        }

        return array('form' => $form->createView(), 'album' => $album);
    }


}
