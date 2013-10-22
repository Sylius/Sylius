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

use Sylius\Bundle\ShippingBundle\Form\ChoiceList\ShippingMethodChoiceListFactoryInterface;
use Sylius\Bundle\ShippingBundle\Resolver\MethodsResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * A select form which allows the user to select
 * a method that supports given shippables aware.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ShippingMethodChoiceType extends AbstractType
{
    /**
     * Supported methods resolver.
     *
     * @var MethodsResolverInterface
     */
    protected $resolver;

    /**
     * Shipping Method Choice List Factory
     *
     * @var ShippingMethodChoiceListFactoryInterface
     */
    protected $choiceFactory;

    /**
     * Constructor.
     *
     * @param MethodsResolverInterface $resolver
     * @param ShippingMethodChoiceListFactoryInterface $choiceFactory
     */
    public function __construct(MethodsResolverInterface $resolver, ShippingMethodChoiceListFactoryInterface $choiceFactory = null)
    {
        $this->resolver = $resolver;
        $this->choiceFactory = $choiceFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $methodsResolver = $this->resolver;
        $choiceFactory = $this->choiceFactory;

        $choiceList = function (Options $options) use ($methodsResolver, $choiceFactory) {
            $methods = $methodsResolver->getSupportedMethods($options['subject'], $options['criteria']);

            return $choiceFactory ? $choiceFactory->createChoiceList($options['subject'], $methods) : new ObjectChoiceList($methods);
        };

        $resolver
            ->setDefaults(array(
                'choice_list' => $choiceList,
                'criteria'    => array()
            ))
            ->setRequired(array(
                'subject',
            ))
            ->setAllowedTypes(array(
                'subject'  => array('Sylius\Bundle\ShippingBundle\Model\ShippingSubjectInterface'),
                'criteria' => array('array')
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_shipping_method_choice';
    }
}
