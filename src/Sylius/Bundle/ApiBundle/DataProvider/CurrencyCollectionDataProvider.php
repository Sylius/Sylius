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
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class CurrencyCollectionDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface
{
    private UserContextInterface $userContext;

    private RepositoryInterface $currencyRepository;

    public function __construct(
        RepositoryInterface $currencyRepository,
        UserContextInterface $userContext
    ) {
        $this->userContext = $userContext;
        $this->currencyRepository = $currencyRepository;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return is_a($resourceClass, CurrencyInterface::class, true);
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        $user = $this->userContext->getUser();

        if ($user instanceof AdminUserInterface && in_array('ROLE_API_ACCESS', $user->getRoles())) {
            return $this->currencyRepository->findAll();
        }

        Assert::keyExists($context, ContextKeys::CHANNEL);

        /** @var ChannelInterface $channel */
        $channel = $context[ContextKeys::CHANNEL];

        return $channel->getCurrencies();
    }
}
