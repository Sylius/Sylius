<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Shipping\Checker;

use Sylius\Component\Shipping\Model\ShippingSubjectInterface;

/**
 * Shipping method rule checker interface.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface RuleCheckerInterface
{
    /**
     * Check if given subject passes the requirements specified in
     * configuration.
     *
     * @param ShippingSubjectInterface $subject
     * @param array                    $configuration
     */
    public function isEligible(ShippingSubjectInterface $subject, array $configuration);

    /**
     * Get the name of configuration form type.
     *
     * @return string
     */
    public function getConfigurationFormType();
}
