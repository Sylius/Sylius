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

use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGenerator;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Doctrine\QueryItemExtension\ProductWithEnabledVariantsExtension;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class ProductItemDataProvider implements RestrictedDataProviderInterface, ItemDataProviderInterface
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private UserContextInterface $userContext,
        private ManagerRegistry $managerRegistry,
        private iterable $itemExtensions
    ) {
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        $user = $this->userContext->getUser();

        if ($user instanceof AdminUserInterface && in_array('ROLE_API_ACCESS', $user->getRoles(), true)) {
            return $this->productRepository->findOneByCode($id);
        }

        Assert::keyExists($context, ContextKeys::CHANNEL);

        /** @var ChannelInterface $channel */
        $channel = $context[ContextKeys::CHANNEL];

        /*
         * Creating custom queryBuilder here makes below findOneByChannelAndCode unusable ( I guess )
         * https://api-platform.com/docs/core/data-providers/#injecting-extensions-pagination-filter-eagerloading-etc
         *
        foreach ($this->itemExtensions as $extension) {
            if ($extension instanceof ProductWithEnabledVariantsExtension) {
                $extension->applyToItem($queryBuilder, $queryNameGenerator, $resourceClass, $identifiers, $operationName, $context);
            }
        }
        */

        return $this->productRepository->findOneByChannelAndCode($channel, $id);
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return is_a($resourceClass, ProductInterface::class, true);
    }
}
