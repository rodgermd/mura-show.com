<?php

namespace Rodger\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Entity\User as BaseUser;

/**
 * Rodger\Bundle\UserBundle\Entity\User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="Rodger\UserBundle\Entity\UserRepository")
 */
class User extends BaseUser
{

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="Rodger\GalleryBundle\Entity\Image", mappedBy="user", cascade={"all"})
     * @var array Images
     */
    protected $images;

    /**
     * @ORM\OneToMany(targetEntity="Rodger\GalleryBundle\Entity\Album", mappedBy="user", cascade={"all"})
     * @var array Albums
     */
    protected $albums;

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
     * Checks if this user equals to given
     * @param User $user
     * @return bool
     */
    public function equals(User $user = null)
    {
        return $user && $this->getId() == $user->getId();
    }

}