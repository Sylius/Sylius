<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Affiliate\Checker;

interface RuleCheckerInterface
{
    /**
     * @param object $subject
     * @param array  $configuration
     *
     * @return bool
     */
    public function isEligible($subject, array $configuration);

    /**
     * @param mixed $subject
     *
     * @return bool
     */
    public function supports($subject);

    /**
     * @return string
     */
    public function getConfigurationFormType();
}
