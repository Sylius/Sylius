<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Promotion\Checker;

use Symfony\Component\VarDumper\VarDumper;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Bundle\OrderBundle\Doctrine\ORM\OrderRepository;
use Sylius\Component\Promotion\Checker\RuleCheckerInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Promotion\Exception\UnsupportedTypeException;
use Sylius\Component\Core\Model\CustomerInterface;

/**
 * Checks if order contains the given variant.
 *
 * @author Jean-Baptiste Blanchon <jean-baptiste@yproximite.com>
 */
class OrderLoyaltyRuleChecker implements RuleCheckerInterface
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
    public function isEligible(PromotionSubjectInterface $subject, array $configuration)
    {
        if (!$subject instanceof OrderInterface) {
            throw new UnsupportedTypeException($subject, 'Sylius\Component\Core\Model\OrderInterface');
        }

        if (null === $customer = $subject->getCustomer()) {
            return false;
        }

        $date = new \DateTime(sprintf('%d %s ago', $configuration['nth'], $configuration['unit']));
        $count  = $this->orderRepository->countByCustomerPaymentStateFromDate($customer, PaymentInterface::STATE_COMPLETED, $date, $configuration['after']);

        if($configuration["equal"] == "up") {
            if ($count >= $configuration['orderNth']) {
                return true;
            }
        }else{
            if ($count <= $configuration['orderNth']) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return 'sylius_promotion_rule_order_loyalty_configuration';
    }
}
