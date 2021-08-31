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

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/** @experimental */
final class CountryCollectionDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface
{
    private RepositoryInterface $countryRepository;

    private UserContextInterface $userContext;

    public function __construct(RepositoryInterface $countryRepository, UserContextInterface $userContext)
    {
        $this->countryRepository = $countryRepository;
        $this->userContext = $userContext;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return is_a($resourceClass, CountryInterface::class, true);
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        $user = $this->userContext->getUser();
        if ($user instanceof AdminUserInterface && in_array('ROLE_API_ACCESS', $user->getRoles(), true)) {
            return $this->countryRepository->findAll();
        }

        /** @var ChannelInterface|null $channel */
        $channel = $context[ContextKeys::CHANNEL] ?? null;
        if ($channel !== null && $channel->getCountries()->count() > 0) {
            return $channel->getCountries();
        }

        return $this->countryRepository->findAll();
    }
}
