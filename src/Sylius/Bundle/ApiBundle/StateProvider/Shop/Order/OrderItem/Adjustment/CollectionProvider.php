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

namespace Sylius\Bundle\ApiBundle\StateProvider\Shop\Order\OrderItem\Adjustment;

use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Doctrine\Common\Collections\Collection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Repository\OrderItemRepositoryInterface;
use Webmozart\Assert\Assert;

/** @implements ProviderInterface<AdjustmentInterface> */
final readonly class CollectionProvider implements ProviderInterface
{
    public function __construct(
        private OrderItemRepositoryInterface $orderItemRepository,
        private SectionProviderInterface $sectionProvider,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array|Collection
    {
        Assert::true(is_a($operation->getClass(), AdjustmentInterface::class, true));
        Assert::isInstanceOf($operation, GetCollection::class);
        Assert::isInstanceOf($this->sectionProvider->getSection(), ShopApiSection::class);

        if (false === isset($uriVariables['id'], $uriVariables['tokenValue'])) {
            return [];
        }

        $orderItem = $this->orderItemRepository->findOneByIdAndOrderTokenValue(
            (int) $uriVariables['id'],
            (string) $uriVariables['tokenValue'],
        );

        if (null === $orderItem) {
            return [];
        }

        return $orderItem->getAdjustmentsRecursively($context['request']->query->get('type'));
    }
}
