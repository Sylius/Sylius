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
     * Constructor.
     *
     * @param MethodsResolverInterface $resolver
     */
    public function __construct(MethodsResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $methodsResolver = $this->resolver;

        $choiceList = function (Options $options) use ($methodsResolver) {
            if (!isset($options['shipment']) && !isset($options['shippables'])) {
                throw new \InvalidArgumentException('One of options "shipment" and "shippables" is required');
            }

            $methods = $methodsResolver->getSupportedMethods($options['shippables'], $options['criteria']);

            return new ObjectChoiceList($methods);
        };

        $resolver
            ->setDefaults(array(
                'choice_list' => $choiceList,
                'criteria'    => array()
            ))
            ->setRequired(array(
                'shippables',
            ))
            ->setAllowedTypes(array(
                'shippables' => array('Sylius\Bundle\ShippingBundle\Model\ShippablesAwareInterface'),
                'criteria'   => array('array')
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
