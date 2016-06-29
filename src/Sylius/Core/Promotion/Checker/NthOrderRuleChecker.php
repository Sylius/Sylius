<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Core\Promotion\Checker;

use Sylius\Core\Model\OrderInterface;
use Sylius\Core\Repository\OrderRepositoryInterface;
use Sylius\Payment\Model\PaymentInterface;
use Sylius\Promotion\Checker\RuleCheckerInterface;
use Sylius\Promotion\Exception\UnsupportedTypeException;
use Sylius\Promotion\Model\PromotionSubjectInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class NthOrderRuleChecker implements RuleCheckerInterface
{
    const TYPE = 'nth_order';

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function isEligible(PromotionSubjectInterface $subject, array $configuration)
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

        return $this->orderRepository->countByCustomerAndPaymentState($customer, PaymentInterface::STATE_COMPLETED) === ($configuration['nth'] - 1);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return 'sylius_promotion_rule_nth_order_configuration';
    }
}
