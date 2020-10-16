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
use Sylius\Bundle\ApiBundle\Provider\CustomerProvider;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;

final class AddressBookDataPersister implements ContextAwareDataPersisterInterface
{
    /** @var ContextAwareDataPersisterInterface */
    private $decoratedDataPersister;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    /** @var CustomerProvider */
    private $customerProvider;

    public function __construct(
        ContextAwareDataPersisterInterface $decoratedDataPersister,
        TokenStorageInterface $tokenStorage,
        CustomerProvider $customerProvider
    ) {
        $this->decoratedDataPersister = $decoratedDataPersister;
        $this->tokenStorage = $tokenStorage;
        $this->customerProvider = $customerProvider;
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof AddressInterface;
    }

    public function persist($data, array $context = [])
    {
        $token = $this->tokenStorage->getToken();
        if ($token === null) {
            throw new TokenNotFoundException();
        }

        /** @var UserInterface $loggedUser */
        $loggedUser = $token->getUser();

        if ($loggedUser instanceof UserInterface) {
            $customer = $this->customerProvider->provide($loggedUser->getEmail());

            $data->setCustomer($customer);
            $data->setFirstName($customer->getFirstName());
            $data->setLastName($customer->getLastName());
            $customer->setDefaultAddress($data);
        }

        return $this->decoratedDataPersister->persist($data, $context);
    }

    public function remove($data, array $context = [])
    {
        return $this->decoratedDataPersister->remove($data, $context);
    }
}
