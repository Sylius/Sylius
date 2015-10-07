<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Shipping\Calculator;

use Sylius\Component\Shipping\Model\ShippingSubjectInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class WeightRateCalculator extends Calculator
{
    /**
     * {@inheritdoc}
     */
    public function calculate(ShippingSubjectInterface $subject, array $configuration)
    {
        return (int) ($configuration['fixed'] + round($configuration['variable'] * ($subject->getShippingWeight() / $configuration['division'])));
    }

    /**
     * {@inheritdoc}
     */
    public function isConfigurable()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return 'sylius_shipping_calculator_weight_rate_configuration';
    }

    /**
     * {@inheritdoc}
     */
    public function setConfiguration(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'division' => 1,
                'fixed'    => 0,
            ))
            ->setRequired(array(
                'variable',
                'division',
                'fixed',
            ))
            ->setAllowedTypes('variable', 'numeric')
            ->setAllowedTypes('division', 'numeric')
            ->setAllowedTypes('fixed', 'numeric')
        ;
    }
}
