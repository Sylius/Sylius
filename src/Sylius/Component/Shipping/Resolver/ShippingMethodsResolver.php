<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Shipping\Resolver;

use Doctrine\Persistence\ObjectRepository;
use Sylius\Component\Shipping\Checker\Eligibility\ShippingMethodEligibilityCheckerInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;
use Sylius\Component\Shipping\Repository\ShippingMethodRepositoryInterface;

final class ShippingMethodsResolver implements ShippingMethodsResolverInterface
{
    public function __construct(
        private ObjectRepository|ShippingMethodRepositoryInterface $shippingMethodRepository,
        private ShippingMethodEligibilityCheckerInterface $eligibilityChecker,
    ) {
        if (!$this->shippingMethodRepository instanceof ShippingMethodRepositoryInterface) {
            @trigger_error(sprintf(
                'Not implementing "%s" in "%s" is deprecated since Sylius 1.13 and will be required in Sylius 2.0.',
                ShippingMethodRepositoryInterface::class,
                get_debug_type($this->shippingMethodRepository),
            ), \E_USER_DEPRECATED);
        }
    }

    public function getSupportedMethods(ShippingSubjectInterface $subject): array
    {
        $methods = [];

        foreach ($this->getEnabledShippingMethods() as $shippingMethod) {
            if ($this->eligibilityChecker->isEligible($subject, $shippingMethod)) {
                $methods[] = $shippingMethod;
            }
        }

        return $methods;
    }

    public function supports(ShippingSubjectInterface $subject): bool
    {
        return true;
    }

    /**
     * @return ShippingMethodInterface[]
     */
    private function getEnabledShippingMethods(): array
    {
        if ($this->shippingMethodRepository instanceof ShippingMethodRepositoryInterface) {
            return $this->shippingMethodRepository->findEnabledWithRules();
        }

        return $this->shippingMethodRepository->findBy(['enabled' => true]);
    }
}
