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

namespace Sylius\Bundle\CoreBundle\Resolver;

use Sylius\Bundle\CoreBundle\Provider\CustomerProviderInterface;
use Sylius\Component\Core\Exception\CustomerNotFoundException;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class CustomerResolver implements CustomerResolverInterface
{
    public function __construct(
        private FactoryInterface $customerFactory,
        private CustomerProviderInterface $customerProvider,
    ) {
    }

    public function resolve(string $email): CustomerInterface
    {
        try {
            return $this->customerProvider->provide($email);
        } catch (CustomerNotFoundException) {
            /** @var CustomerInterface $customer */
            $customer = $this->customerFactory->createNew();
            $customer->setEmail($email);

            return $customer;
        }
    }
}
