<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Shipping\Resolver;

use Sylius\Component\Registry\PrioritizedServiceRegistryInterface;
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class CompositeMethodsResolver implements ShippingMethodsResolverInterface
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
    public function getSupportedMethods(ShippingSubjectInterface $shippingSubject)
    {
        /** @var ShippingMethodsResolverInterface $resolver */
        foreach ($this->resolversRegistry->all() as $resolver) {
            if ($resolver->supports($shippingSubject)) {
                return $resolver->getSupportedMethods($shippingSubject);
            }
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ShippingSubjectInterface $subject)
    {
        /** @var ShippingMethodsResolverInterface $resolver */
        foreach ($this->resolversRegistry->all() as $resolver) {
            if ($resolver->supports($subject)) {
                return true;
            }
        }

        return false;
    }
}
