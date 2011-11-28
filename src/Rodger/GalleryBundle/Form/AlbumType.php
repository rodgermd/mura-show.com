<?php
namespace Rodger\GalleryBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class AlbumType extends AbstractType
{
  public $keywords_autocomplete_source;
  public function buildForm(FormBuilder $builder, array $options)
  {
    $builder->add('name')
            ->add('is_private', 'checkbox', array('required' => false))
            ->add('keywords', 'text', array(
                'required' => false, 
                'attr' => array('source' => $this->keywords_autocomplete_source, 'class' => 'keywords autocomplete')));
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