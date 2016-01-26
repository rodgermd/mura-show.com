<?php
namespace Rodger\GalleryBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

class BulkImages extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'images',
            EntityType::class,
            array(
                'class' => 'RodgerGalleryBundle:Image',
                'query_builder' => $options['data']->getQueryBuilder(),
                'expanded' => true,
                'multiple' => true
            )
        )
            ->add(
                'action',
                'choice',
                array('choices' => $options['data']::getActions(), 'empty_value' => 'Please select an action')
            );
    }
}

?>