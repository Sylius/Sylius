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

namespace Sylius\Component\Core\Resolver;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\PaymentInterface as CorePaymentInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Payment\Exception\UnresolvedDefaultPaymentMethodException;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Resolver\DefaultPaymentMethodResolverInterface;
use Webmozart\Assert\Assert;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class DefaultPaymentMethodResolver implements DefaultPaymentMethodResolverInterface
{
    /**
     * @var PaymentMethodRepositoryInterface
     */
    protected $paymentMethodRepository;

    /**
     * @param PaymentMethodRepositoryInterface $paymentMethodRepository
     */
    public function __construct(PaymentMethodRepositoryInterface $paymentMethodRepository)
    {
        $this->paymentMethodRepository = $paymentMethodRepository;
    }

    /**
     * {@inheritdoc}
     *
     * @throws UnresolvedDefaultPaymentMethodException
     */
    public function getDefaultPaymentMethod(PaymentInterface $subject): PaymentMethodInterface
    {
        // @var CorePaymentInterface $subject
        Assert::isInstanceOf($subject, CorePaymentInterface::class);

        /** @var ChannelInterface $channel */
        $channel = $subject->getOrder()->getChannel();

        $paymentMethods = $this->paymentMethodRepository->findEnabledForChannel($channel);
        if (empty($paymentMethods)) {
            throw new UnresolvedDefaultPaymentMethodException();
        }

        return $paymentMethods[0];
    }
}
