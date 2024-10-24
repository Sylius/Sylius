<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ShippingBundle\Tests\Stub;

use Sylius\Bundle\ShippingBundle\Attribute\AsShippingMethodRuleChecker;
use Sylius\Component\Shipping\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;

#[AsShippingMethodRuleChecker(type: 'test', label: 'Test', formType: 'SomeFormType', priority: 20)]
final class ShippingMethodRuleCheckerStub implements RuleCheckerInterface
{
    public function isEligible(ShippingSubjectInterface $subject, array $configuration): bool
    {
        return true;
    }
}
