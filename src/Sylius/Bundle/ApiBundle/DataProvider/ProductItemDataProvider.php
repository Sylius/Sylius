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

namespace Sylius\Bundle\ApiBundle\DataProvider;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Webmozart\Assert\Assert;

final class ProductItemDataProvider implements RestrictedDataProviderInterface, ItemDataProviderInterface
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private UserContextInterface $userContext,
    ) {
    }

    public function getItem(string $resourceClass, $id, ?string $operationName = null, array $context = [])
    {
        $user = $this->userContext->getUser();

        if ($user instanceof AdminUserInterface && in_array('ROLE_API_ACCESS', $user->getRoles(), true)) {
            return $this->productRepository->findOneByCode($id);
        }

        Assert::keyExists($context, ContextKeys::CHANNEL);

        /** @var ChannelInterface $channel */
        $channel = $context[ContextKeys::CHANNEL];

        return $this->productRepository->findOneByChannelAndCodeWithAvailableAssociations($channel, $id);
    }

    public function supports(string $resourceClass, ?string $operationName = null, array $context = []): bool
    {
        return is_a($resourceClass, ProductInterface::class, true);
    }
}
