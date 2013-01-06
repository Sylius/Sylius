<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShippingBundle\Form\Type;

use Sylius\Bundle\ShippingBundle\Calculator\DelegatingShippingChargeCalculator;
use Sylius\Bundle\ShippingBundle\Form\EventListener\BuildShippingMethodFormListener;
use Sylius\Bundle\ShippingBundle\Model\ShippingMethod;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Shipping method form type.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class ShippingMethodType extends AbstractType
{
    /**
     * Data class.
     *
     * @var string
     */
    protected $dataClass;

    /**
     * Delegating calculator.
     *
     * @var DelegatingShippingChargeCalculator
     */
    protected $delegatingCalculator;

    /**
     * Constructor.
     *
     * @param string                             $dataClass
     * @param DelegatingShippingChargeCalculator $delegatingCalculator
     */
    public function __construct($dataClass, DelegatingShippingChargeCalculator $delegatingCalculator)
    {
        $this->dataClass = $dataClass;
        $this->delegatingCalculator = $delegatingCalculator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addEventSubscriber(new BuildShippingMethodFormListener($this->delegatingCalculator, $builder->getFormFactory()))
            ->add('category', 'sylius_shipping_category_choice', array(
                'required' => false
            ))
            ->add('enabled', 'checkbox', array(
                'required' => false,
            ))
            ->add('requirement', 'choice', array(
                'choices'  => ShippingMethod::getRequirementLabels(),
                'multiple' => false,
                'expanded' => true
            ))
            ->add('name', 'text')
            ->add('calculator', 'sylius_shipping_calculator_choice')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => $this->dataClass
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_shipping_method';
    }
}
