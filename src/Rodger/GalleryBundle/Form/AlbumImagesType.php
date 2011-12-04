<?php
namespace Rodger\GalleryBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class AlbumImagesType extends AbstractType
{
  protected $images, $thumbnail_template = 'small';
  const ACTION_DELETE = 'delete';
  public function buildForm(FormBuilder $builder, array $options)
  {
    $builder->add('images', 'image_choice', array(
        'required' => false, 
        'multiple' => true, 
        'expanded' => true,
        'choices' => $this->getImagesChoices(),
        'template' => $this->getThumbnailTemplate()
            ))
            ->add('bulk_actions', 'choice', array(
                'choices' => $this->getActionChoices() 
            ));
  }
  
  public function getName() { return 'album_images'; }
  
  /**
   * Sets images
   * @param mixed $images 
   */
  public function setImages($images) {
    $this->images = $images;
  }
  
  /**
   * Gets images choice
   * @return array 
   */
  private function getImagesChoices() {
    $result = array();
    foreach($this->images as $image) {
      $result[$image->getId()] = $image;
    }
    return $result;
  }
  
  private function getActionChoices() {
    return array(self::ACTION_DELETE => 'Delete selected images');
  }
  
  public function getThumbnailTemplate() {
    return $this->thumbnail_template;
  }
  public function setThumbnailTemplate($name) {
    $this->thumbnail_template = $name;
  }
  
}
?>
