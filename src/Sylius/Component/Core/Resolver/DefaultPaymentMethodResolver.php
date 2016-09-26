<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Resolver;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\PaymentInterface as CorePaymentInterface;
use Sylius\Component\Payment\Exception\UnresolvedDefaultPaymentMethodException;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\Resolver\DefaultPaymentMethodResolverInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class DefaultPaymentMethodResolver implements DefaultPaymentMethodResolverInterface
{
    /**
     * @var RepositoryInterface
     */
    protected $paymentMethodRepository;

    /**
     * @param RepositoryInterface $paymentMethodRepository
     */
    public function __construct(RepositoryInterface $paymentMethodRepository)
    {
        $this->paymentMethodRepository = $paymentMethodRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultPaymentMethod(PaymentInterface $subject)
    {
        /** @var CorePaymentInterface $subject */
        Assert::isInstanceOf($subject, CorePaymentInterface::class);

        $paymentMethods = $this->paymentMethodRepository->findBy(['enabled' => true]);
        if (empty($paymentMethods)) {
            throw new UnresolvedDefaultPaymentMethodException();
        }

        /** @var ChannelInterface $channel */
        $channel = $subject->getOrder()->getChannel();

        foreach ($paymentMethods as $paymentMethod) {
            if ($channel->hasPaymentMethod($paymentMethod)) {
                return $paymentMethod;
            }
        }

        throw new UnresolvedDefaultPaymentMethodException();
    }
}
