<?php

namespace Rodger\GalleryBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;
use Gedmo\Mapping\Annotation\Timestampable;
use Rodger\GalleryBundle\Entity\Album;
use Rodger\GalleryBundle\Entity\Tag;
use Rodger\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Rodger\GalleryBundle\Entity\Image
 *
 * @ORM\Table(name="images", uniqueConstraints={
 *   @ORM\UniqueConstraint(name="filename_unique",columns={"filename"})
 * },
 * indexes={@ORM\Index(name="year_month_idx", columns={"year", "month"})})
 * )
 * @ORM\Entity(repositoryClass="Rodger\GalleryBundle\Entity\ImageRepository")
 * @Vich\Uploadable
 */
class Image
{

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Vich\Uploadable
     */
    private $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=100)
     */
    private $name;

    /**
     * @var string $filename
     *
     * @ORM\Column(name="filename", type="string", length=50)
     */
    private $filename;

    /**
     * Uploaded at
     *
     * @var \DateTime $uploadedAt
     * @Timestampable(on="create")
     * @ORM\Column(name="uploaded_at",type="datetime", nullable=true)
     */
    private $uploadedAt;

    /**
     * Datetime when picture was taken (shot)
     *
     * @var \DateTime $takenAt
     * @ORM\Column(name="taken_at",type="datetime", nullable=true)
     */
    private $takenAt;

    /**
     * Year of image taken date
     *
     * @var integer $year
     * @ORM\Column(type="integer", nullable=true)
     */
    private $year;

    /**
     * month of image taken date
     *
     * @var integer $month
     * @ORM\Column(type="integer", nullable=true)
     */
    private $month;

    /**
     * Related Album
     * @ORM\ManyToOne(targetEntity="Album", inversedBy="images")
     * @ORM\JoinColumn(name="album_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $album;

    /**
     * @var integer $album_id
     * @ORM\Column(type="integer", nullable=true)
     */
    private $album_id;

    /**
     * Related Tags
     *
     * @var PersistentCollection Tags
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="images")
     * @ORM\JoinTable(name="image_tags",
     *      joinColumns={@ORM\JoinColumn(name="image_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tag", referencedColumnName="name", onDelete="CASCADE")}
     *      )
     */
    private $tags;

    /**
     * Is private flag
     *
     * @var boolean $private
     * @ORM\Column(name="is_private", type="boolean")
     */
    private $private = false;

    /**
     * Related User
     *
     * @var User $user
     * @ORM\ManyToOne(targetEntity="Rodger\UserBundle\Entity\User", inversedBy="images")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="set null")
     */
    private $user;

    /**
     * @var array $exifData
     * @ORM\Column(name="exif_data", type="array")
     */
    private $exifData;

    /**
     * @Assert\File(
     *     maxSize="6M",
     *     mimeTypes={"image/png", "image/jpeg", "image/pjpeg"}
     * )
     * @Vich\UploadableField(mapping="image", fileNameProperty="filename")
     * @var UploadedFile $image
     */
    private $file;


    /**
     * Related keywords
     *
     * @var string
     */
    protected $keywords;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
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
     * Get filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Gets is_private flag
     *
     * @return boolean
     */
    public function isPrivate()
    {
        return $this->private;
    }

    /**
     * Sets is_private flag
     *
     * @param boolean $private
     */
    public function setPrivate($private)
    {
        $this->private = (bool)$private;
    }

    /**
     * Gets uploaded at
     *
     * @return \DateTime
     */
    public function getUploadedAt()
    {
        return $this->uploadedAt;
    }

    /**
     * Sets uploaded at
     *
     * @param \DateTime $uploadedAt
     */
    public function setUploadedAt($uploadedAt)
    {
        $this->uploadedAt = $uploadedAt;
    }

    /**
     * Gets taken at
     *
     * @return \DateTime
     */
    public function getTakenAt()
    {
        return $this->takenAt;
    }

    /**
     * Sets taken at
     *
     * @param \DateTime $takenAt
     */
    public function setTakenAt($takenAt)
    {
        $this->takenAt = $takenAt;
        $this->year    = $takenAt->format('Y');
        $this->month   = $takenAt->format('m');
    }

    /**
     * Sets related album
     *
     * @param Album $album
     */
    public function setAlbum(Album $album)
    {
        $this->album    = $album;
        $this->album_id = $album->getId();
    }

    /**
     * Gets related Album
     *
     * @return Album
     */
    public function getAlbum()
    {
        return $this->album;
    }

    /**
     * Sets related Tags
     *
     * @param array $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * Adds a Tag into collection
     *
     * @param Tag $tag
     */
    public function addTag(Tag $tag)
    {
        $this->tags[] = $tag;
    }

    /**
     * Gets related Tags
     *
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Gets uploader User
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Sets uploader User
     *
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * Set exif data
     *
     * @param array $data
     */
    public function setExifData(array $data)
    {
        $this->exifData = $data;
    }

    /**
     * Get exif data
     *
     * @return array
     */
    public function getExifData()
    {
        return $this->exifData;
    }

    /**
     * Gets keywords
     *
     * @return string
     */
    public function getKeywords()
    {
        return implode(
            ', ',
            array_map(
                function (Tag $tag) {
                    return $tag->__toString();
                },
                $this->tags->toArray()
            )
        );
    }

    /**
     * Gets keywords
     *
     * @return string
     */
    public function getKeywordsRaw()
    {
        return $this->keywords;
    }

    /**
     * @param string $keywords
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
    }

    /**
     * @param File $file
     */
    public function setFile(File $file)
    {
        $this->file = $file;
        $this->name = pathinfo($file->getFilename(), PATHINFO_FILENAME);
    }

    /**
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }
}