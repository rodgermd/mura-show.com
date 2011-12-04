<?php

namespace Rodger\GalleryBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Exception\FormException;
use Symfony\Component\Form\Extension\Core\ChoiceList\ArrayChoiceList;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\Extension\Core\EventListener\FixRadioInputListener;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\Extension\Core\DataTransformer\ScalarToChoiceTransformer;
use Symfony\Component\Form\Extension\Core\DataTransformer\ScalarToBooleanChoicesTransformer;
use Symfony\Component\Form\Extension\Core\DataTransformer\ArrayToChoicesTransformer;
use Symfony\Component\Form\Extension\Core\DataTransformer\ArrayToBooleanChoicesTransformer;

class ImageChoiceType extends AbstractType {

  public function getName() {
    return 'image_choice';
  }

  public function getDefaultOptions(array $options) {
    return array('multiple' => true, 'expanded' => true, 'template' => '');
  }

  public function getParent(array $options) {
    return 'choice';
  }

  public function buildForm(FormBuilder $builder, array $options) {
    if ($options['choice_list'] && !$options['choice_list'] instanceof ChoiceListInterface) {
      throw new FormException('The "choice_list" must be an instance of "Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface".');
    }

    if (!$options['choice_list']) {
      $options['choice_list'] = new ArrayChoiceList($options['choices']);
    }

    if ($options['expanded']) {
      // Load choices already if expanded
      $options['choices'] = $options['choice_list']->getChoices();

      foreach ($options['choices'] as $choice => $value) {
        if ($options['multiple']) {
          $builder->add((string) $choice, 'image_checkbox', array(
              'value' => $choice,
              'label' => (string) $value,
              // The user can check 0 or more checkboxes. If required
              // is true, he is required to check all of them.
              'required' => false,
              'thumbnail' => $value->thumbnail($options['template'])
          ));
        } else {
          $builder->add((string) $choice, 'radio', array(
              'value' => $choice,
              'label' => $value,
          ));
        }
      }
    }

    // empty value
    if ($options['multiple'] || $options['expanded']) {
      // never use and empty value for these cases
      $emptyValue = null;
    } elseif (false === $options['empty_value']) {
      // an empty value should be added but the user decided otherwise
      $emptyValue = null;
    } elseif (null === $options['empty_value']) {
      // user did not made a decision, so we put a blank empty value
      $emptyValue = $options['required'] ? null : '';
    } else {
      // empty value has been set explicitly
      $emptyValue = $options['empty_value'];
    }

    $builder
            ->setAttribute('choice_list', $options['choice_list'])
            ->setAttribute('preferred_choices', $options['preferred_choices'])
            ->setAttribute('multiple', $options['multiple'])
            ->setAttribute('expanded', $options['expanded'])
            ->setAttribute('required', $options['required'])
            ->setAttribute('empty_value', $emptyValue)
    ;

    if ($options['expanded']) {
      if ($options['multiple']) {
        $builder->appendClientTransformer(new ArrayToBooleanChoicesTransformer($options['choice_list']));
      } else {
        $builder
                ->appendClientTransformer(new ScalarToBooleanChoicesTransformer($options['choice_list']))
                ->addEventSubscriber(new FixRadioInputListener(), 10)
        ;
      }
    } else {
      if ($options['multiple']) {
        $builder->appendClientTransformer(new ArrayToChoicesTransformer());
      } else {
        $builder->appendClientTransformer(new ScalarToChoiceTransformer());
      }
    }
  }

}

?>
