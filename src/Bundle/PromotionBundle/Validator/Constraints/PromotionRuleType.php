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

namespace Sylius\Bundle\PromotionBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

final class PromotionRuleType extends Constraint
{
    public string $invalidType = 'sylius.promotion_rule.invalid_type';

    public function validatedBy(): string
    {
        return 'sylius_promotion_rule_type';
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
