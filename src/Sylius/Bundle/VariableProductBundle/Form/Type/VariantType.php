<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\VariableProductBundle\Form\Type;

use Sylius\Bundle\VariableProductBundle\Form\EventListener\BuildVariantFormListener;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Product variant form type.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class VariantType extends AbstractType
{
    /**
     * Data class.
     *
     * @var string
     */
    protected $dataClass;

    /**
     * Constructor.
     *
     * It's important to set the data class that was configured inside 'config.yml'.
     * This will be done automatically when using this class, but if you would like to extend it,
     * remember to pass '%sylius.model.variant.class%' as an argument inside service definition.
     *
     * @param string $dataClass FQCN of the product variant model
     */
    public function __construct($dataClass)
    {
        $this->dataClass = $dataClass;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('presentation', 'text', array(
                'required' => false,
                'label'    => 'sylius.form.variant.presentation'
            ))
            ->add('availableOn', 'datetime', array(
                'date_format' => 'y-M-d',
                'date_widget' => 'choice',
                'time_widget' => 'text',
                'label'       => 'sylius.form.variant.available_on'
            ))
        ;

        if (!$options['master']) {
            $builder->addEventSubscriber(new BuildVariantFormListener($builder->getFormFactory()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => $this->dataClass,
                'master'     => false
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_variant';
    }
}
