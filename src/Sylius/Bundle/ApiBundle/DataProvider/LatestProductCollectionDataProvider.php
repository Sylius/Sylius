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
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Webmozart\Assert\Assert;

final class LatestProductCollectionDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface
{
    /** @var ProductRepositoryInterface */
    private $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return is_a($resourceClass, ProductInterface::class, true) && $operationName === 'get_latest';
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): iterable
    {
        Assert::keyExists($context, ContextKeys::CHANNEL);
        Assert::keyExists($context, ContextKeys::LOCALE_CODE);

        $channel = $context[ContextKeys::CHANNEL];
        $localeCode = $context[ContextKeys::LOCALE_CODE];

        return $this->productRepository->findLatestByChannel($channel, $localeCode, 3);
    }
}
