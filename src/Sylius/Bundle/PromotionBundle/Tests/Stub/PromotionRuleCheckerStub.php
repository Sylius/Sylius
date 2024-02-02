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

namespace Sylius\Bundle\PromotionBundle\Tests\Stub;

use Sylius\Bundle\PromotionBundle\Attribute\AsPromotionRuleChecker;
use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

#[AsPromotionRuleChecker(type: 'test', label: 'Test', formType: 'SomeFormType', priority: 40)]
final class PromotionRuleCheckerStub implements RuleCheckerInterface
{
    public function isEligible(PromotionSubjectInterface $subject, array $configuration): bool
    {
        return true;
    }
}
