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

namespace Sylius\Bundle\CoreBundle\Doctrine\ORM;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\OrderBundle\Doctrine\ORM\OrderRepository as BaseOrderRepository;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use SyliusLabs\AssociationHydrator\AssociationHydrator;

class OrderRepository extends BaseOrderRepository implements OrderRepositoryInterface
{
    /** @var AssociationHydrator */
    protected $associationHydrator;

    public function __construct(EntityManager $entityManager, Mapping\ClassMetadata $class)
    {
        parent::__construct($entityManager, $class);

        $this->associationHydrator = new AssociationHydrator($entityManager, $class);
    }

    public function createListQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->addSelect('customer')
            ->leftJoin('o.customer', 'customer')
            ->andWhere('o.state != :state')
            ->setParameter('state', OrderInterface::STATE_CART)
        ;
    }

    public function createByCustomerIdQueryBuilder($customerId): QueryBuilder
    {
        return $this->createListQueryBuilder()
            ->andWhere('o.customer = :customerId')
            ->setParameter('customerId', $customerId)
        ;
    }

    public function createByCustomerAndChannelIdQueryBuilder($customerId, $channelId): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.customer = :customerId')
            ->andWhere('o.channel = :channelId')
            ->andWhere('o.state != :state')
            ->setParameter('customerId', $customerId)
            ->setParameter('channelId', $channelId)
            ->setParameter('state', OrderInterface::STATE_CART)
        ;
    }

    public function findByCustomer(CustomerInterface $customer): array
    {
        return $this->createByCustomerIdQueryBuilder($customer->getId())
            ->getQuery()
            ->getResult()
        ;
    }

    public function findForCustomerStatistics(CustomerInterface $customer): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.customer = :customerId')
            ->andWhere('o.state = :state')
            ->setParameter('customerId', $customer->getId())
            ->setParameter('state', OrderInterface::STATE_FULFILLED)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOneForPayment($id): ?OrderInterface
    {
        return $this->createQueryBuilder('o')
            ->addSelect('payments')
            ->addSelect('paymentMethods')
            ->leftJoin('o.payments', 'payments')
            ->leftJoin('payments.method', 'paymentMethods')
            ->andWhere('o.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function countByCustomerAndCoupon(
        CustomerInterface $customer,
        PromotionCouponInterface $coupon,
        bool $includeCancelled = false
    ): int {
        $states = [OrderInterface::STATE_CART];
        if ($coupon->isReusableFromCancelledOrders()) {
            $states[] = OrderInterface::STATE_CANCELLED;
        }

        return (int) $this->createQueryBuilder('o')
            ->select('COUNT(o.id)')
            ->andWhere('o.customer = :customer')
            ->andWhere('o.promotionCoupon = :coupon')
            ->andWhere('o.state NOT IN (:states)')
            ->setParameter('customer', $customer)
            ->setParameter('coupon', $coupon)
            ->setParameter('states', $states)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function countByCustomer(CustomerInterface $customer): int
    {
        return (int) $this->createQueryBuilder('o')
            ->select('COUNT(o.id)')
            ->andWhere('o.customer = :customer')
            ->andWhere('o.state NOT IN (:states)')
            ->setParameter('customer', $customer)
            ->setParameter('states', [OrderInterface::STATE_CART, OrderInterface::STATE_CANCELLED])
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function findOneByNumberAndCustomer(string $number, CustomerInterface $customer): ?OrderInterface
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.customer = :customer')
            ->andWhere('o.number = :number')
            ->setParameter('customer', $customer)
            ->setParameter('number', $number)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findCartByChannel($id, ChannelInterface $channel): ?OrderInterface
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.id = :id')
            ->andWhere('o.state = :state')
            ->andWhere('o.channel = :channel')
            ->setParameter('id', $id)
            ->setParameter('state', OrderInterface::STATE_CART)
            ->setParameter('channel', $channel)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findLatestCartByChannelAndCustomer(ChannelInterface $channel, CustomerInterface $customer): ?OrderInterface
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.state = :state')
            ->andWhere('o.channel = :channel')
            ->andWhere('o.customer = :customer')
            ->setParameter('state', OrderInterface::STATE_CART)
            ->setParameter('channel', $channel)
            ->setParameter('customer', $customer)
            ->addOrderBy('o.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findLatestNotEmptyCartByChannelAndCustomer(
        ChannelInterface $channel,
        CustomerInterface $customer
    ): ?OrderInterface {
        return $this->createQueryBuilder('o')
            ->andWhere('o.state = :state')
            ->andWhere('o.channel = :channel')
            ->andWhere('o.customer = :customer')
            ->andWhere('o.items IS NOT EMPTY')
            ->setParameter('state', OrderInterface::STATE_CART)
            ->setParameter('channel', $channel)
            ->setParameter('customer', $customer)
            ->addOrderBy('o.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function getTotalSalesForChannel(ChannelInterface $channel): int
    {
        return (int) $this->createQueryBuilder('o')
            ->select('SUM(o.total)')
            ->andWhere('o.channel = :channel')
            ->andWhere('o.state = :state')
            ->setParameter('channel', $channel)
            ->setParameter('state', OrderInterface::STATE_FULFILLED)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function getTotalPaidSalesForChannel(ChannelInterface $channel): int
    {
        return (int) $this->createQueryBuilder('o')
            ->select('SUM(o.total)')
            ->andWhere('o.channel = :channel')
            ->andWhere('o.paymentState = :state')
            ->setParameter('channel', $channel)
            ->setParameter('state', OrderPaymentStates::STATE_PAID)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function getTotalPaidSalesForChannelInPeriod(
        ChannelInterface $channel,
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate
    ): int {
        return (int) $this->createQueryBuilder('o')
            ->select('SUM(o.total)')
            ->andWhere('o.channel = :channel')
            ->andWhere('o.paymentState = :state')
            ->andWhere('o.checkoutCompletedAt >= :startDate')
            ->andWhere('o.checkoutCompletedAt <= :endDate')
            ->setParameter('channel', $channel)
            ->setParameter('state', OrderPaymentStates::STATE_PAID)
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function countFulfilledByChannel(ChannelInterface $channel): int
    {
        return (int) $this->createQueryBuilder('o')
            ->select('COUNT(o.id)')
            ->andWhere('o.channel = :channel')
            ->andWhere('o.state = :state')
            ->setParameter('channel', $channel)
            ->setParameter('state', OrderInterface::STATE_FULFILLED)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function countPaidByChannel(ChannelInterface $channel): int
    {
        return (int) $this->createQueryBuilder('o')
            ->select('COUNT(o.id)')
            ->andWhere('o.channel = :channel')
            ->andWhere('o.paymentState = :state')
            ->setParameter('channel', $channel)
            ->setParameter('state', OrderPaymentStates::STATE_PAID)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function countPaidForChannelInPeriod(
        ChannelInterface $channel,
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate
    ): int {
        return (int) $this->createQueryBuilder('o')
            ->select('COUNT(o.id)')
            ->andWhere('o.channel = :channel')
            ->andWhere('o.paymentState = :state')
            ->andWhere('o.checkoutCompletedAt >= :startDate')
            ->andWhere('o.checkoutCompletedAt <= :endDate')
            ->setParameter('channel', $channel)
            ->setParameter('state', OrderPaymentStates::STATE_PAID)
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function findLatestInChannel(int $count, ChannelInterface $channel): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.channel = :channel')
            ->andWhere('o.state != :state')
            ->addOrderBy('o.checkoutCompletedAt', 'DESC')
            ->setParameter('channel', $channel)
            ->setParameter('state', OrderInterface::STATE_CART)
            ->setMaxResults($count)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOrdersUnpaidSince(\DateTimeInterface $terminalDate): array
    {
        return $this->createQueryBuilder('o')
            ->where('o.checkoutState = :checkoutState')
            ->andWhere('o.paymentState != :paymentState')
            ->andWhere('o.state = :orderState')
            ->andWhere('o.checkoutCompletedAt < :terminalDate')
            ->setParameter('checkoutState', OrderCheckoutStates::STATE_COMPLETED)
            ->setParameter('paymentState', OrderPaymentStates::STATE_PAID)
            ->setParameter('orderState', OrderInterface::STATE_NEW)
            ->setParameter('terminalDate', $terminalDate)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findCartForSummary($id): ?OrderInterface
    {
        /** @var OrderInterface $order */
        $order = $this->createQueryBuilder('o')
            ->andWhere('o.id = :id')
            ->andWhere('o.state = :state')
            ->setParameter('id', $id)
            ->setParameter('state', OrderInterface::STATE_CART)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        $this->associationHydrator->hydrateAssociations($order, [
            'adjustments',
            'items',
            'items.adjustments',
            'items.units',
            'items.units.adjustments',
            'items.variant',
            'items.variant.optionValues',
            'items.variant.optionValues.translations',
            'items.variant.product',
            'items.variant.product.translations',
            'items.variant.product.images',
            'items.variant.product.options',
            'items.variant.product.options.translations',
        ]);

        return $order;
    }

    public function findCartForAddressing($id): ?OrderInterface
    {
        /** @var OrderInterface $order */
        $order = $this->createQueryBuilder('o')
            ->andWhere('o.id = :id')
            ->andWhere('o.state = :state')
            ->setParameter('id', $id)
            ->setParameter('state', OrderInterface::STATE_CART)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        $this->associationHydrator->hydrateAssociations($order, [
            'items',
            'items.variant',
            'items.variant.product',
            'items.variant.product.translations',
        ]);

        return $order;
    }

    public function findCartForSelectingShipping($id): ?OrderInterface
    {
        /** @var OrderInterface $order */
        $order = $this->createQueryBuilder('o')
            ->andWhere('o.id = :id')
            ->andWhere('o.state = :state')
            ->setParameter('id', $id)
            ->setParameter('state', OrderInterface::STATE_CART)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        $this->associationHydrator->hydrateAssociations($order, [
            'items',
            'items.variant',
            'items.variant.product',
            'items.variant.product.translations',
        ]);

        return $order;
    }

    public function findCartForSelectingPayment($id): ?OrderInterface
    {
        /** @var OrderInterface $order */
        $order = $this->createQueryBuilder('o')
            ->andWhere('o.id = :id')
            ->andWhere('o.state = :state')
            ->setParameter('id', $id)
            ->setParameter('state', OrderInterface::STATE_CART)
            ->getQuery()
            ->getOneOrNullResult()
        ;

        $this->associationHydrator->hydrateAssociations($order, [
            'items',
            'items.variant',
            'items.variant.product',
            'items.variant.product.translations',
        ]);

        return $order;
    }

    public function findCartByTokenValue(string $tokenValue): ?BaseOrderInterface
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.state = :state')
            ->andWhere('o.tokenValue = :tokenValue')
            ->setParameter('state', OrderInterface::STATE_CART)
            ->setParameter('tokenValue', $tokenValue)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
