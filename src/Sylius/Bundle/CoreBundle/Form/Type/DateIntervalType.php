<?php

namespace Sylius\Bundle\CoreBundle\Form\Type;

use Sylius\Bundle\CoreBundle\Form\DataTransformer\DateIntervalTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DateIntervalType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('frequency', 'text')
            ->add('unit', 'choice', array(
                'choices' => $options['units'],
                'empty_value' => '',
            ))
            ->addModelTransformer(new DateIntervalTransformer($options))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'units' => array(
                    'd' => 'Days',
                    'm' => 'Months',
                    'y' => 'Years',
                )
            ))
            ->setAllowedTypes(array(
                'units' => 'array'
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_date_interval';
    }
}
