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
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/** @experimental */
final class AddressDataPersister implements ContextAwareDataPersisterInterface
{
    /** @var ContextAwareDataPersisterInterface */
    private $decoratedDataPersister;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    public function __construct(
        ContextAwareDataPersisterInterface $decoratedDataPersister,
        TokenStorageInterface $tokenStorage
    ) {
        $this->decoratedDataPersister = $decoratedDataPersister;
        $this->tokenStorage = $tokenStorage;
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof AddressInterface;
    }

    public function persist($data, array $context = [])
    {
        $loggedUser = $this->getUser();
        if ($loggedUser instanceof ShopUserInterface) {
            /** @var CustomerInterface $customer */
            $customer = $loggedUser->getCustomer();
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

    private function getUser(): ?UserInterface
    {
        $token = $this->tokenStorage->getToken();
        if ($token === null) {
            return null;
        }

        /** @var UserInterface $loggedUser */
        $loggedUser = $token->getUser();

        return $loggedUser;
    }
}
