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

namespace Sylius\Component\Core\Promotion\Checker\Rule;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Promotion\Exception\UnsupportedTypeException;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

final class NthOrderRuleChecker implements RuleCheckerInterface
{
    public const TYPE = 'nth_order';

    public function __construct(private OrderRepositoryInterface $orderRepository)
    {
    }

    /**
     * @throws UnsupportedTypeException
     */
    public function isEligible(PromotionSubjectInterface $subject, array $configuration): bool
    {
        if (!$subject instanceof OrderInterface) {
            throw new UnsupportedTypeException($subject, OrderInterface::class);
        }

        if (!isset($configuration['nth']) || !is_int($configuration['nth'])) {
            return false;
        }

        $customer = $subject->getCustomer();
        if (null === $customer) {
            return false;
        }

        //eligible if it is first order of guest and the promotion is on first order
        if (null === $customer->getId()) {
            return 1 === $configuration['nth'];
        }

        return $this->orderRepository->countByCustomer($customer) === ($configuration['nth'] - 1);
    }
}
