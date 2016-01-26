<?php
namespace Rodger\GalleryBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Rodger\GalleryBundle\Entity\Upload;

class AlbumType extends AbstractType
{
    public $keywords_autocomplete_source;

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name')
            ->add('private', CheckboxType::class, array('required' => false))
            ->add('keywords', TextType::class, array('required' => false, 'attr' => array('source' => $this->keywords_autocomplete_source, 'class' => 'keywords autocomplete')));
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
}

?>
