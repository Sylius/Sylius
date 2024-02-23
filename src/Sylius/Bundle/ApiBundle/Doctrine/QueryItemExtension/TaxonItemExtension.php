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

namespace Sylius\Bundle\ApiBundle\Doctrine\QueryItemExtension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

final class TaxonItemExtension implements QueryItemExtensionInterface
{
    public function __construct(private UserContextInterface $userContext)
    {
    }

    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        ?string $operationName = null,
        array $context = [],
    ) {
        if (!is_a($resourceClass, TaxonInterface::class, true)) {
            return;
        }

        $user = $this->userContext->getUser();
        if ($user !== null && in_array('ROLE_API_ACCESS', $user->getRoles(), true)) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $enabledParameter = $queryNameGenerator->generateParameterName('enabled');
        $childAlias = $queryNameGenerator->generateJoinAlias('child');

        $queryBuilder->addSelect($childAlias);
        $queryBuilder->leftJoin(sprintf('%s.children', $rootAlias), $childAlias, 'WITH', sprintf('%s.enabled = :%s', $childAlias, $enabledParameter));
        $queryBuilder->andWhere(sprintf('%s.enabled = :%s', $rootAlias, $enabledParameter));
        $queryBuilder->setParameter($enabledParameter, true);
    }
}
