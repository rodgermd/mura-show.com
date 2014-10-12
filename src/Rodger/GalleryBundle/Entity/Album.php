<?php

namespace Rodger\GalleryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Rodger\UserBundle\Entity\User;
use Rodger\GalleryBundle\Entity\Image;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Rodger\GalleryBundle\Entity\Album
 *
 * @ORM\Table(name="albums")
 * @ORM\Entity(repositoryClass="Rodger\GalleryBundle\Entity\AlbumRepository")
 */
class Album
{

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=100)
     */
    private $name;

    /**
     * related images
     * @ORM\OneToMany(targetEntity="Image", mappedBy="album")
     * @ORM\OrderBy({"taken_at" = "ASC", "uploaded_at" = "ASC"})
     */
    private $images;

    /**
     * Created at
     * @var \DateTime $created_at
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * Is private flag
     * @var boolean $is_private
     * @ORM\Column(name="is_private", type="boolean")
     */
    private $is_private = false;

    /**
     * Related User
     * @var User $user
     * @ORM\ManyToOne(targetEntity="Rodger\UserBundle\Entity\User", inversedBy="albums")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="cascade")
     */
    private $user;

    /**
     * @var string $slug
     * @ORM\Column(type="string", nullable=true)
     * @Gedmo\Slug(fields={"name"})
     */
    private $slug;

    /**
     * Related keywords
     * @var string
     */
    protected $keywords;

    /**
     * Related Tags
     * @var array Tags
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="albums")
     * @ORM\JoinTable(name="album_tags",
     *      joinColumns={@ORM\JoinColumn(name="album_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tag", referencedColumnName="name")}
     *      )
     */
    private $tags;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->created_at = new \DateTime();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

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
     * Gets is_provate flag
     * @return boolean
     */
    public function getIsPrivate()
    {
        return $this->is_private;
    }

    /**
     * Sets is_private flag
     * @param boolean $is_private
     */
    public function setIsPrivate($is_private)
    {
        $this->is_private = (bool)$is_private;
    }

    /**
     * Gets created at
     * @return datetime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Gets uploader User
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Sets uploader User
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
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
     * Adds Image
     * @param Image $image
     */
    public function addImage(Image $image)
    {
        $this->images[] = $image;
    }

    /**
     * Gets related Tags
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Adds Image
     * @param Image $image
     */
    public function addTag(Tag $tag)
    {
        $this->tags[] = $tag;
    }

    /**
     * Sets related Tags
     * @param array $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * Gets slug value
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    public function getKeywords()
    {
        if ($this->keywords) {
            return $this->keywords;
        }
        $result = array();
        foreach ($this->tags as $tag) {
            $result[] = (string)$tag;
        }
        $this->keywords = implode(", ", $result);

        return $this->keywords;
    }

    /**
     * @param string $keywords
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
    }

    public function __toString()
    {
        return $this->name;
    }
}