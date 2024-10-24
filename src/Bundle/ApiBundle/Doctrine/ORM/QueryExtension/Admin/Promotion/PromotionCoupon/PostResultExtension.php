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

namespace Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Admin\Promotion\PromotionCoupon;

use ApiPlatform\Doctrine\Orm\Extension\QueryResultItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface;

/** @implements QueryResultItemExtensionInterface<PromotionCouponInterface> */
final readonly class PostResultExtension implements QueryResultItemExtensionInterface
{
    public function __construct(private SectionProviderInterface $sectionProvider)
    {
    }

    /**
     * @param array<array-key, mixed> $identifiers
     * @param array<array-key, mixed> $context
     */
    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        ?Operation $operation = null,
        array $context = [],
    ): void {
    }

    /** @param array<array-key, mixed> $context */
    public function supportsResult(string $resourceClass, ?Operation $operation = null, array $context = []): bool
    {
        return
            $operation instanceof Post &&
            $this->sectionProvider->getSection() instanceof AdminApiSection &&
            is_a($resourceClass, PromotionCouponInterface::class, true)
        ;
    }

    /** @param array<array-key, mixed> $context */
    public function getResult(QueryBuilder $queryBuilder, ?string $resourceClass = null, ?Operation $operation = null, array $context = []): ?object
    {
        return null;
    }
}
