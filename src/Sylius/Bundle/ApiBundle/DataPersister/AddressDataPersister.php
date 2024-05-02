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

namespace Sylius\Bundle\ApiBundle\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\MissingTokenException;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;

final class AddressDataPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(
        private ContextAwareDataPersisterInterface $decoratedDataPersister,
        private UserContextInterface $userContext,
    ) {
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof AddressInterface;
    }

    public function persist($data, array $context = [])
    {
        $user = $this->userContext->getUser();
        if ($user === null) {
            throw new MissingTokenException('JWT Token not found');
        }

        /** @var CustomerInterface|null $customer */
        $customer = $user instanceof ShopUserInterface ? $user->getCustomer() : null;
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
        $user = $this->userContext->getUser();
        if ($user === null) {
            throw new MissingTokenException('JWT Token not found');
        }

        return $this->decoratedDataPersister->remove($data, $context);
    }
}
