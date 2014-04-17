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
 * Shipping charges calculator.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface CalculatorInterface
{
    /**
     * Is this shipping calculator configurable?
     *
     * @return bool
     */
    public function isConfigurable();

    /**
     * Calculate the shipping charge for given subject and configuration.
     *
     * @param ShippingSubjectInterface $subject
     * @param array                    $configuration
     *
     * @return integer
     */
    public function calculate(ShippingSubjectInterface $subject, array $configuration);

    /**
     * Get calculator configuration form type, if any required.
     *
     * @return string
     */
    public function getConfigurationFormType();

    /**
     * Define the configuration.
     *
     * @param OptionsResolverInterface $resolver
     */
    public function setConfiguration(OptionsResolverInterface $resolver);
}
