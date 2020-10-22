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

namespace Sylius\Bundle\ApiBundle\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Customer\Context\CustomerContextInterface;

/** @experimental */
final class AddressDataPersister implements ContextAwareDataPersisterInterface
{
    /** @var ContextAwareDataPersisterInterface */
    private $decoratedDataPersister;

    /** @var CustomerContextInterface */
    private $customerContext;

    public function __construct(
        ContextAwareDataPersisterInterface $decoratedDataPersister,
        CustomerContextInterface $customerContext
    ) {
        $this->decoratedDataPersister = $decoratedDataPersister;
        $this->customerContext = $customerContext;
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof AddressInterface;
    }

    public function persist($data, array $context = [])
    {
        /** @var CustomerInterface|null $customer */
        $customer = $this->customerContext->getCustomer();
        if ($customer !== null) {
            /** @var AddressInterface $data */
            $data->setCustomer($customer);

            if ($customer->getDefaultAddress() === null) {
                $customer->setDefaultAddress($data);
            }
        }

        return $this->decoratedDataPersister->persist($data, $context);
    }

    public function remove($data, array $context = [])
    {
        return $this->decoratedDataPersister->remove($data, $context);
    }
}
