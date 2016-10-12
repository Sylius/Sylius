<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Payment\Resolver;

use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Registry\PrioritizedServiceRegistryInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
final class CompositeMethodsResolver implements PaymentMethodsResolverInterface
{
    /**
     * @var PrioritizedServiceRegistryInterface
     */
    private $resolversRegistry;

    /**
     * @param PrioritizedServiceRegistryInterface $resolversRegistry
     */
    public function __construct(PrioritizedServiceRegistryInterface $resolversRegistry)
    {
        $this->resolversRegistry = $resolversRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedMethods(PaymentInterface $payment)
    {
        /** @var PaymentMethodsResolverInterface $resolver */
        foreach ($this->resolversRegistry->all() as $resolver) {
            if ($resolver->supports($payment)) {
                return $resolver->getSupportedMethods($payment);
            }
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function supports(PaymentInterface $payment)
    {
        /** @var PaymentMethodsResolverInterface $resolver */
        foreach ($this->resolversRegistry->all() as $resolver) {
            if ($resolver->supports($payment)) {
                return true;
            }
        }

        return false;
    }
}
