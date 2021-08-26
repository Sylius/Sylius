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
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Bundle\ApiBundle\Entity\Product\Product;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Webmozart\Assert\Assert;
use Sylius\Bundle\ApiBundle\DataProvider\Helpers\ProductDataProviderHelper;

/** @experimental */
final class ProductItemDataProvider implements RestrictedDataProviderInterface, ItemDataProviderInterface
{
    /** @var ProductRepositoryInterface */
    private $productRepository;

    /** @var UserContextInterface */
    private $userContext;

    public function __construct(ProductRepositoryInterface $productRepository, UserContextInterface $userContext)
    {
        $this->productRepository = $productRepository;
        $this->userContext = $userContext;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        $user = $this->userContext->getUser();

        if ($user instanceof AdminUserInterface && in_array('ROLE_API_ACCESS', $user->getRoles(), true)) {
            /** @var Product $product */
            $product =  $this->productRepository->findOneByCode($id);
        } else {
            Assert::keyExists($context, ContextKeys::CHANNEL);

            /** @var ChannelInterface $channel */
            $channel = $context[ContextKeys::CHANNEL];
            /** @var Product $product */
            $product =  $this->productRepository->findOneByChannelAndCode($channel, $id);
        }

        $product = ProductDataProviderHelper::setCustomPropertiesToProduct($product);

        return $product;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return is_a($resourceClass, Product::class, true);
    }
}
