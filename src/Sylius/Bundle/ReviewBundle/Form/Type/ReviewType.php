<?php

namespace Sylius\Bundle\ReviewBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ReviewType extends AbstractType
{
    protected $dataClass;

    public function __construct($dataClass)
    {
        $this->dataClass = $dataClass;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('rating', 'choice', array('choices' => array_combine(range(1,5), range(1,5))))
            ->add('title', 'text')
            ->add('comment', 'textarea');

        if ($options['allow_guest']) {
            $builder->add('guest_reviewer', 'sylius_guest_reviewer');
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
           'data_class' => $this->dataClass,
           'allow_guest' => false
        ));
    }

    public function getName()
    {
        return 'sylius_review';
    }
}
