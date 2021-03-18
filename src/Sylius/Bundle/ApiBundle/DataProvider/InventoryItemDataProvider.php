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
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class InventoryItemDataProvider implements ItemDataProviderInterface, RestrictedDataProviderInterface
{
    /** @var ProductVariantRepositoryInterface */
    private $productVariantRepository;

    /** @var UserContextInterface */
    private $userContext;

    public function __construct(ProductVariantRepositoryInterface $productVariantRepository, UserContextInterface $userContext)
    {
        $this->productVariantRepository = $productVariantRepository;
        $this->userContext = $userContext;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return is_a($resourceClass, ProductVariantInterface::class, true) && str_starts_with($operationName, 'admin_inventory_');
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        $user = $this->userContext->getUser();

        Assert::keyExists($context, ContextKeys::LOCALE_CODE);
        $localeCode = $context[ContextKeys::LOCALE_CODE];

        if ($user instanceof AdminUserInterface && in_array('ROLE_API_ACCESS', $user->getRoles(), true)) {
            return $this->productVariantRepository->findOneInventoryItem($id, $localeCode);
        }

        return null;
    }
}
