<?php
namespace Rodger\GalleryBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ImageType extends AbstractType
{
    public $keywords_autocomplete_source;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name')
            ->add('private', CheckboxType::class, array('required' => false))
            ->add('file', FileType::class, array('required' => false, 'data_class' => null, 'attr' => array('multiple' => 'multiple')))
            ->add('keywords', 'text', array('required' => false, 'attr' => array('source' => $this->keywords_autocomplete_source, 'class' => 'keywords autocomplete')));
    }

    /**
     * Sets keywords autocomplete source url
     *
     * @param string $url
     */
    public function setKeywordsAutocompleteSource($url)
    {
        $this->keywords_autocomplete_source = $url;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('csrf_protection' => false,));
    }
}

?>
