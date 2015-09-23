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
     *
     * @return bool
     */
    public function isEligible(ShippingSubjectInterface $subject, array $configuration);

    /**
     * @return string
     */
    public function getConfigurationFormType();
}
