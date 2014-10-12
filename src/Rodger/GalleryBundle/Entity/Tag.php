<?php

namespace Rodger\GalleryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Rodger\GalleryBundle\Entity\Tag
 *
 * @ORM\Table(name="tags")
 * @ORM\Entity(repositoryClass="Rodger\GalleryBundle\Entity\TagRepository")
 */
class Tag
{

    /**
     * @var string $name
     * @ORM\Id
     * @ORM\Column(name="name", type="string", length=50)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="Image", mappedBy="tags")
     * @var array $images
     */
    private $images;

    /**
     * @ORM\ManyToMany(targetEntity="Album", mappedBy="tags")
     * @var array $albums
     */
    private $albums;

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Gets related Images
     * @return array
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Sets related Images
     * @param array $images
     */
    public function setImages($images)
    {
        $this->images = $images;
    }

    /**
     * Add Image into collection
     * @param Image $image
     */
    public function addImage(Image $image)
    {
        $this->images[] = $image;
    }

    /**
     * Gets related Albums
     * @return array
     */
    public function getAlbums()
    {
        return $this->albums;
    }

    /**
     * Sets related Albums
     * @param array $albums
     */
    public function setAlbums($albums)
    {
        $this->albums = $albums;
    }

    /**
     * Add Album into collection
     * @param Album $album
     */
    public function addAlbum(Album $album)
    {
        $this->albums[] = $album;
    }

    public function __toString()
    {
        return $this->getName();
    }
}