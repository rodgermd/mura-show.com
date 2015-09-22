<?php
/**
 * Created by JetBrains PhpStorm.
 * User: rodger
 * Date: 24.03.13
 * Time: 14:59
 * To change this template use File | Settings | File Templates.
 */

namespace Rodger\GalleryBundle\Namer;


use Rodger\GalleryBundle\Entity\Image;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\DirectoryNamerInterface;

class DirectoryNamer implements DirectoryNamerInterface
{

    /**
     * Creates a directory name for the file being uploaded.
     *
     * @param object          $object
     * @param PropertyMapping $mapping
     *
     * @return string The directory name.
     */
    public function directoryName($object, PropertyMapping $mapping)
    {
        /** @var Image $object */
        return $mapping->getUploadDestination() . '/' . $object->getAlbum()->getId();
    }
}