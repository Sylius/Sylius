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

namespace Sylius\Bundle\ApiBundle\DataProvider;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\MissingTokenException;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\AddressRepositoryInterface;

/** @experimental */
final class AddressItemDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface
{
    private AddressRepositoryInterface $addressRepository;

    private UserContextInterface $userContext;

    public function __construct(AddressRepositoryInterface $addressRepository, UserContextInterface $userContext)
    {
        $this->addressRepository = $addressRepository;
        $this->userContext = $userContext;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return is_a($resourceClass, AddressInterface::class, true);
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        $user = $this->userContext->getUser();
        if ($user === null) {
            throw new MissingTokenException('JWT Token not found');
        }

        /** @var CustomerInterface|null $customer */
        $customer = $user instanceof ShopUserInterface ? $user->getCustomer() : null;

        if (
            $user instanceof ShopUserInterface &&
            in_array('ROLE_USER', $user->getRoles(), true) &&
            $customer !== null
        ) {
            return $this->addressRepository->findOneByCustomer((string) $id, $customer);
        }

        if ($user instanceof AdminUserInterface && in_array('ROLE_API_ACCESS', $user->getRoles(), true)) {
            return $this->addressRepository->findOneBy(['id' => $id]);
        }

        return null;
    }
}
