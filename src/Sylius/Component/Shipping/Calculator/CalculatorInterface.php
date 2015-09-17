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
interface CalculatorInterface
{
    /**
     * @return bool
     */
    public function isConfigurable();

    /**
     * @param ShippingSubjectInterface $subject
     * @param array                    $configuration
     *
     * @return int
     */
    public function calculate(ShippingSubjectInterface $subject, array $configuration);

    /**
     * @return string
     */
    public function getConfigurationFormType();

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setConfiguration(OptionsResolverInterface $resolver);
}
