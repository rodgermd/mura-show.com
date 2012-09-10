<?php

namespace Rodger\GalleryBundle\Form;

use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class ImageCheckboxType extends AbstractType {

  public function getDefaultOptions(array $options) {
    return array('thumbnail' => '');
  }

  public function getParent() {
    return 'checkbox';
  }

  public function getName() {
    return 'image_checkbox';
  }

  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder->setAttribute('thumbnail', $options['thumbnail']);
  }

  public function buildView(FormView $view, FormInterface $form, array $options) {
    $view->vars['thumbnail'] = $form->getAttribute('thumbnail');
  }

}

?>
