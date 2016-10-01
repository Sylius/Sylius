<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\OrderProcessing;

use Sylius\Component\Core\Model\OrderInterface as CoreOrderInterface;
use Sylius\Component\Currency\Converter\CurrencyConverterInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Payment\Exception\UnresolvedDefaultPaymentMethodException;
use Sylius\Component\Payment\Factory\PaymentFactoryInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Resolver\DefaultPaymentMethodResolverInterface;
use Webmozart\Assert\Assert;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class OrderPaymentProcessor implements OrderProcessorInterface
{
    /**
     * @var PaymentFactoryInterface
     */
    private $paymentFactory;

    /**
     * @var DefaultPaymentMethodResolverInterface
     */
    private $defaultPaymentMethodResolver;

    /**
     * @var CurrencyConverterInterface
     */
    private $currencyConverter;

    /**
     * @param PaymentFactoryInterface $paymentFactory
     * @param DefaultPaymentMethodResolverInterface $defaultPaymentMethodResolver
     * @param CurrencyConverterInterface $currencyConverter
     *
     */
    public function __construct(PaymentFactoryInterface $paymentFactory,
                                DefaultPaymentMethodResolverInterface $defaultPaymentMethodResolver,
                                CurrencyConverterInterface $currencyConverter)
    {
        $this->paymentFactory = $paymentFactory;
        $this->defaultPaymentMethodResolver = $defaultPaymentMethodResolver;
        $this->currencyConverter = $currencyConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function process(OrderInterface $order)
    {
        /** @var CoreOrderInterface $order */
        Assert::isInstanceOf($order, CoreOrderInterface::class);

        if (OrderInterface::STATE_CANCELLED === $order->getState()) {
            return;
        }

        $newPayment = $order->getLastNewPayment();
        if (null !== $newPayment) {
            $newPayment->setCurrencyCode($order->getCurrencyCode());
            $newPayment->setAmount($this->currencyConverter->convertFromBase(
                $order->getTotal(),
                $order->getCurrencyCode()
            ));

            return;
        }

        $this->createNewPayment($order);
    }

    /**
     * @param CoreOrderInterface $order
     */
    private function createNewPayment(CoreOrderInterface $order)
    {
        /** @var $payment PaymentInterface */
        $payment = $this->paymentFactory->createWithAmountAndCurrencyCode(
            $this->currencyConverter->convertFromBase($order->getTotal(), $order->getCurrencyCode()),
            $order->getCurrencyCode()
        );

        $paymentMethod = $this->getDefaultPaymentMethod($payment, $order);
        $lastPayment = $this->getLastPayment($order);

        if (null !== $lastPayment) {
            $paymentMethod = $lastPayment->getMethod();
        }

        if (null === $paymentMethod) {
            return;
        }

        $payment->setMethod($paymentMethod);
        $order->addPayment($payment);
    }

    /**
     * @param OrderInterface $order
     *
     * @return bool|PaymentInterface
     */
    private function getLastPayment(OrderInterface $order)
    {
        return $this->getLastPaymentWithState($order, PaymentInterface::STATE_CANCELLED) ?: $this->getLastPaymentWithState($order, PaymentInterface::STATE_FAILED);
    }

    /**
     * @param CoreOrderInterface $order
     * @param string $state
     *
     * @return null|PaymentInterface
     */
    private function getLastPaymentWithState(CoreOrderInterface $order, $state)
    {
        $lastPayment = $order->getPayments()->filter(function (PaymentInterface $payment) use ($state) {
            return $payment->getState() === $state;
        })->last();

        return $lastPayment !== false ? $lastPayment : null;
    }

    /**
     * @param PaymentInterface $payment
     * @param OrderInterface $order
     *
     * @return null|PaymentMethodInterface
     */
    private function getDefaultPaymentMethod(PaymentInterface $payment, OrderInterface $order)
    {
        try {
            $payment->setOrder($order);
            $paymentMethod = $this->defaultPaymentMethodResolver->getDefaultPaymentMethod($payment);

            return $paymentMethod;
        } catch (UnresolvedDefaultPaymentMethodException $exception) {
            return null;
        }
    }
}
