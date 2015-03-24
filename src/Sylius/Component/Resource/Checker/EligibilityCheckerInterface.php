<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Resource\Checker;

use Sylius\Component\Resource\Model\RuleAwareInterface;

/**
 * Eligibility checker interface.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
interface EligibilityCheckerInterface
{
    /**
     * @param object             $subject
     * @param RuleAwareInterface $object
     *
     * @return bool
     */
    public function isEligible($subject, RuleAwareInterface $object);
}
