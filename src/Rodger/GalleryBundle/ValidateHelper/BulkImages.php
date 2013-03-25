<?php
namespace Rodger\GalleryBundle\ValidateHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Rodger\GalleryBundle\Entity\Image;
use Rodger\GalleryBundle\Manager\UploadManager;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContext;
use Rodger\GalleryBundle\Convert\Converter;

/**
 * @Assert\Callback(methods={"areImagesValid"})
 */
class BulkImages {
  const ACTION_DELETE = 'delete';
  const ACTION_SET_PRIVATE = 'set_publish';
  const ACTION_UNSET_PRIVATE = 'unset_private';
  const ACTION_ROTATE_PLUS90 = 'rotate90';
  const ACTION_ROTATE_MINUS90 = 'rotate-90';
  
  /**
   * @var type 
   */
  public $images;
  
  /**
   * @Assert\Choice(callback = "getActionsKeys")
   * @var string $action 
   */
  public $action;
  
  /**
   *
   * @var \Doctrine\ORM\QueryBuilder $query_builder
   */
  protected $query_builder;
  protected $em;
  protected $container;
  /** @var UploadManager object */
  protected $upload_manager;
  
  public function __construct($query, EntityManager $em, Container $container) {
    $this->em = $em;
    $this->images = new ArrayCollection();
    $this->query_builder = $query;
    $this->container = $container;
    $this->upload_manager = $container->get('gallery.upload_manager');
  }
  
  public static function getActions() {
    return array(
        self::ACTION_DELETE => 'Delete images',
        self::ACTION_SET_PRIVATE => 'Set private',
        self::ACTION_UNSET_PRIVATE => 'Unset private',
        self::ACTION_ROTATE_PLUS90 => 'Rotate +90',
        self::ACTION_ROTATE_MINUS90 => 'Rotate -90'
      );
  }
  
  public static function getActionsKeys() {
    return array_keys(self::getActions());
  }
  
  public function getQueryBuilder() {
    return $this->query_builder;
  }
  
  public function areImagesValid(ExecutionContext $context) {
    $captured_ids = array_map(function($image){ return $image->getId(); }, $this->images->toArray());
    
    $property_path = $context->getPropertyPath() . '.images';

    if (!count($captured_ids)) {
      $context->addViolationAt($property_path, 'Please select at least one image!', array(), null);
      return;
    }
    
    $count = $this->query_builder->andWhere($this->query_builder->expr()->in('i.id', $captured_ids))->select('COUNT(i.id)')->getQuery()->getSingleScalarResult();
    
    if (!$count) $context->addViolation('Please select images from the list!', array(), null);
  }
  
  public function process() {
    foreach($this->images as $image) {
      switch($this->action) {
        case self::ACTION_DELETE: $this->em->remove($image); break;
        case self::ACTION_SET_PRIVATE:
          $image->setIsPrivate(true);
          $this->em->persist($image);
          break;
        case self::ACTION_UNSET_PRIVATE:
          $image->setIsPrivate(false);
          $this->em->persist($image);
          break;
        case self::ACTION_ROTATE_PLUS90:
          $this->upload_manager->rotate_image($image, 90);
          break;
        case self::ACTION_ROTATE_MINUS90:
          $this->upload_manager->rotate_image($image, -90);
          break;
      }
    }
  }


}
?>
