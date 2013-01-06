<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShippingBundle\Calculator;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Base calculator.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
abstract class Calculator implements ShippingChargeCalculatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function isConfigurable()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function buildConfigurationForm(FormBuilderInterface $builder)
    {
        // Nothing to do here...
    }

    /**
     * {@inheritdoc}
     */
    public function buildConfiguration(OptionsResolverInterface $resolver)
    {
        // Nothing to do here...
    }
}
