<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Payment\Resolver;

use Sylius\Payment\Model\PaymentInterface;
use Sylius\Registry\PrioritizedServiceRegistryInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class CompositeMethodsResolver implements MethodsResolverInterface
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
        foreach ($this->resolversRegistry->all() as $resolver) {
            if ($resolver->supports($payment)) {
                
                return true;
            }
        }

        return false;
    }
}
