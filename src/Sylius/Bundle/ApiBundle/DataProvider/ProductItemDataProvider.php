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
use Sylius\Bundle\ApiBundle\Helper\UserContextHelperInterface;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Webmozart\Assert\Assert;

final class ProductItemDataProvider implements RestrictedDataProviderInterface, ItemDataProviderInterface
{
    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var UserContextHelperInterface */
    private $userContextHelper;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        UserContextHelperInterface $userContextHelper
    ) {
        $this->productRepository = $productRepository;
        $this->userContextHelper = $userContextHelper;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        if ($this->userContextHelper->hasAdminRoleApiAccess()) {
            return $this->productRepository->findOneByCode($id);
        }

        Assert::keyExists($context, ContextKeys::CHANNEL);
        Assert::keyExists($context, ContextKeys::LOCALE_CODE);

        /** @var ChannelInterface $channel */
        $channel = $context[ContextKeys::CHANNEL];
        $locale = $context[ContextKeys::LOCALE_CODE];

        return $this->productRepository->findOneByChannelAndSlug($channel, $locale, $id);
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return is_a($resourceClass, ProductInterface::class, true);
    }
}
