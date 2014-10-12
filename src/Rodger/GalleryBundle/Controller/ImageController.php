<?php
namespace Rodger\GalleryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

use Rodger\GalleryBundle\Entity\Album;
use Rodger\GalleryBundle\Entity\Image;

use Rodger\GalleryBundle\Form\ImageType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Vich\UploaderBundle\Storage\FileSystemStorage;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

/**
 * @Route("/image")
 */
class ImageController extends CommonController
{

    /**
     * @Route("/edit/{id}", name="image.edit", requirements={"id"="\d+"})
     * @Secure(roles="ROLE_USER")
     * @Template
     * @param Image $image
     * @return array
     */
    public function editAction(Image $image)
    {
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
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function updateAction(Image $image)
    {
        $type = new ImageType();
        $type->setKeywordsAutocompleteSource($this->generateUrl('keywords.autocomplete'));
        $form = $this->createForm($type, $image);

        $old_image = clone $image;

        $form->submit($this->getRequest());

        if ($form->isValid()) {
            $keywords = array_filter(array_map('trim', explode(",", $image->getKeywords())));
            $tags = array();
            if (count($keywords)) {
                foreach ($keywords as $keyword) {
                    $tags [] = $this->em->getRepository('RodgerGalleryBundle:Tag')->getOrCreate($keyword);
                }
            }

            $image->setTags($tags);

            if ($image->getUploadRootDir() != $old_image->getUploadRootDir()) {
                //rename ($old_image->getAbsolutePath(), $image->getAbsolutePath());
                exec(
                    sprintf(
                        "mv %s %s/",
                        $old_image->thumbnail('*', true),
                        $image->getAlbum()->getThumbnailsFolder(true)
                    )
                );
            }

            $this->em->persist($image);
            $this->em->flush();

            return $this->redirect($this->generateUrl('image.edit', array('id' => $image->getId())));
        }

        return array('form' => $form->createView(), 'image' => $image);
    }

    /**
     * @Route("/{id}/original", name="image.original", requirements={"id"="\d+"})
     */
    public function originalAction(Image $image)
    {
        /** @var FileSystemStorage $storage */
        $storage = $this->get('vich_uploader.storage.file_system');
        $file = $storage->resolvePath($image, 'file');


        $response = new Response(file_get_contents($file));
        $response->headers->set('Content-Type', 'image/png');

        return $response;
    }

    /**
     * @Route("/{id}/delete", name="image.delete", requirements={"id"="\d+"})
     * @Secure(roles="ROLE_USER")
     * @param Image $image
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function deleteAction(Image $image)
    {
        if (!$image->getUser()->equals($this->getUser())) {
            throw new AccessDeniedException();
        }
        $redirect = $this->generateUrl('album.show', array('slug' => $image->getAlbum()->getSlug()));
        $em = $this->getDoctrine()->getManager();
        $em->remove($image);
        $em->flush();

        return $this->redirect($redirect);
    }

    /**
     * @Route("/{album}/{id}", name="image.show")
     * @Template
     */
    public function showAction(Image $image)
    {
        return array('image' => $image, 'album' => $image->getAlbum());
    }


}

?>
