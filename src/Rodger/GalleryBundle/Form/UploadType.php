<?php
namespace Rodger\GalleryBundle\Form;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface;

class UploadType extends AbstractType
{
  public $keywords_autocomplete_source;
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder->add('file', 'file', array('required' => false))
            ->add('default_name')
            ->add('is_private', 'checkbox', array('required' => false))
            ->add('keywords', 'text', array(
                'required' => false, 
                'attr' => array('source' => $this->keywords_autocomplete_source, 'class' => 'keywords autocomplete')))
            ;
  }
  
  public function getName() { return 'upload'; }
  
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
