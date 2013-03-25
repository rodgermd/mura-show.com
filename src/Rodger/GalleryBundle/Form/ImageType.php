<?php
namespace Rodger\GalleryBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ImageType extends AbstractType
{
  public $keywords_autocomplete_source;
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder->add('name')
            ->add('is_private', 'checkbox', array('required' => false))
            ->add('file', 'file', array('required' => false, 'data_class' => null))
            ->add('keywords', 'text', array(
                'required' => false, 
                'attr' => array('source' => $this->keywords_autocomplete_source, 'class' => 'keywords autocomplete')));
  }
  
  public function getName() { return 'image'; }
  
  /**
   * Sets keywords autocomplete source url
   * @param string $url
   */
  public function setKeywordsAutocompleteSource($url)
  {
    $this->keywords_autocomplete_source = $url;
  }

  public function setDefaultOptions(OptionsResolverInterface $resolver)
  {
    $resolver->setDefaults(array(
      'csrf_protection'   => false,
    ));
  }
}
?>
