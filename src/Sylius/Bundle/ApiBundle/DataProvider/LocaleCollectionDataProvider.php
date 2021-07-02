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
use Doctrine\Common\Collections\Collection;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class LocaleCollectionDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface
{
    /** @var RepositoryInterface */
    private $localeRepository;

    /** @var UserContextInterface */
    private $userContext;

    public function __construct(
        RepositoryInterface $localeRepository,
        UserContextInterface $userContext
    ) {
        $this->localeRepository = $localeRepository;
        $this->userContext = $userContext;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return is_a($resourceClass, LocaleInterface::class, true);
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        $user = $this->userContext->getUser();

        if ($user instanceof AdminUserInterface && in_array('ROLE_API_ACCESS', $user->getRoles())) {
            return $this->localeRepository->findAll();
        }

        Assert::keyExists($context, ContextKeys::CHANNEL);

        /** @var ChannelInterface $channel */
        $channel = $context[ContextKeys::CHANNEL];

        return $channel->getLocales();
    }
}
