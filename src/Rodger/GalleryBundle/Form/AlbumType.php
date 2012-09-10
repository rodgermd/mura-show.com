<?php
namespace Rodger\GalleryBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Rodger\GalleryBundle\Entity\Upload;

class AlbumType extends AbstractType
{
  public $keywords_autocomplete_source;
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $upload_type = new UploadType(new Upload());
    $upload_type->setKeywordsAutocompleteSource($this->keywords_autocomplete_source);
    
    $builder->add('name')
            ->add('is_private', 'checkbox', array('required' => false))
            ->add('keywords', 'text', array(
                'required' => false, 
                'attr' => array('source' => $this->keywords_autocomplete_source, 'class' => 'keywords autocomplete')))
            ->add('upload', $upload_type, array('required' => false));
  }
  
  public function getName() { return 'album'; }
  
  /**
   * Sets keywords autocomplete source url
   * @param string $url
   */
  public function setKeywordsAutocompleteSource($url)
  {
    $this->keywords_autocomplete_source = $url;
  }
}
?>
