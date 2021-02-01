<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Promotion\Checker\Rule;

use Sylius\Component\Promotion\Model\CountablePromotionSubjectInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

final class CartQuantityRuleChecker implements RuleCheckerInterface
{
    public const TYPE = 'cart_quantity';

    public function isEligible(PromotionSubjectInterface $subject, array $configuration): bool
    {
        if (!$subject instanceof CountablePromotionSubjectInterface) {
            return false;
        }

        // Legacy operator to avoid BC break
        if (!isset($configuration['comparison_operator'])) {
            $configuration['comparison_operator'] = '>=';
        }

        $promotionSubjectCount = $subject->getPromotionSubjectCount();

        if ('>=' === $configuration['comparison_operator']) {
            return $promotionSubjectCount >= $configuration['count'];
        }

        if ('===' === $configuration['comparison_operator']) {
            return $promotionSubjectCount === $configuration['count'];
        }

        if ('!==' === $configuration['comparison_operator']) {
            return $promotionSubjectCount !== $configuration['count'];
        }

        if ('<' === $configuration['comparison_operator']) {
            return $promotionSubjectCount < $configuration['count'];
        }

        if ('<=' === $configuration['comparison_operator']) {
            return $promotionSubjectCount <= $configuration['count'];
        }

        if ('>' === $configuration['comparison_operator']) {
            return $promotionSubjectCount > $configuration['count'];
        }

        return false;
    }
}
