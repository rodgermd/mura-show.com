<?php
/**
 * Created by JetBrains PhpStorm.
 * User: rodger
 * Date: 24.03.13
 * Time: 14:59
 * To change this template use File | Settings | File Templates.
 */

namespace Rodger\GalleryBundle\Namer;


use Vich\UploaderBundle\Naming\DirectoryNamerInterface;

class DirectoryNamer implements DirectoryNamerInterface
{

    /**
     * Creates a directory name for the file being uploaded.
     *
     * @param object $obj The object the upload is attached to.
     * @param string $field The name of the uploadable field to generate a name for.
     * @param string $uploadDir The upload directory set in config
     * @return string The directory name.
     */
    public function directoryName($obj, $field, $uploadDir)
    {
        return $uploadDir . '/' . $obj->getAlbumId();
    }
}