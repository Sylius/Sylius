<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Affiliate\Checker;

use Sylius\Component\Affiliate\Checker\RuleCheckerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Payment\Model\PaymentInterface;

class NthOrderRuleChecker implements RuleCheckerInterface
{
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
    public function isEligible($subject, array $configuration)
    {
        /* @var $subject OrderInterface */
        if (null === $customer = $subject->getCustomer()) {
            return false;
        }

        return $this->orderRepository->countByCustomerAndPaymentState($customer, PaymentInterface::STATE_COMPLETED) === ($configuration['nth'] - 1);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return 'sylius_affiliate_rule_nth_order_configuration';
    }

    /**
     * {@inheritdoc}
     */
    public function supports($subject)
    {
        return $subject instanceof OrderInterface;
    }
}