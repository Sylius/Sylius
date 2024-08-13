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

namespace Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Order;

use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\OrderInterface;

final readonly class VisitorBasedExtension implements QueryItemExtensionInterface
{
    public function __construct(
        private SectionProviderInterface $sectionProvider,
        private UserContextInterface $userContext,
    ) {
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
        if (!is_a($resourceClass, OrderInterface::class, true)) {
            return;
        }

        if (!$this->sectionProvider->getSection() instanceof ShopApiSection) {
            return;
        }

        if (null !== $this->userContext->getUser()) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $customerJoinName = $queryNameGenerator->generateJoinAlias('customer');
        $userJoinName = $queryNameGenerator->generateJoinAlias('user');
        $createdByGuestParameterName = $queryNameGenerator->generateParameterName('createdByGuest');

        $queryBuilder
            ->leftJoin(sprintf('%s.customer', $rootAlias), $customerJoinName)
            ->leftJoin(sprintf('%s.user', $customerJoinName), $userJoinName)
            ->andWhere(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->isNull($userJoinName),
                    $queryBuilder->expr()->eq(sprintf('%s.createdByGuest', $rootAlias), sprintf(':%s', $createdByGuestParameterName)),
                ),
            )
            ->setParameter($createdByGuestParameterName, true)
        ;
    }
}
