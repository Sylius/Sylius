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

namespace Sylius\Bundle\ApiBundle\Doctrine\QueryCollectionExtension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\ContextAwareQueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Webmozart\Assert\Assert;

final class TaxonCollectionExtension implements ContextAwareQueryCollectionExtensionInterface
{
    public function __construct(private UserContextInterface $userContext)
    {
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?string $operationName = null,
        array $context = [],
    ): void {
        if (!is_a($resourceClass, TaxonInterface::class, true)) {
            return;
        }

        $user = $this->userContext->getUser();
        if ($user instanceof AdminUserInterface && in_array('ROLE_API_ACCESS', $user->getRoles(), true)) {
            return;
        }

        Assert::keyExists($context, ContextKeys::CHANNEL);
        $channelMenuTaxon = $context[ContextKeys::CHANNEL]->getMenuTaxon();

        $enabledParameterName = $queryNameGenerator->generateParameterName('enabled');
        $parentCodeParameterName = $queryNameGenerator->generateParameterName('parentCode');

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder
            ->addSelect('child')
            ->innerJoin(sprintf('%s.parent', $rootAlias), 'parent')
            ->leftJoin(sprintf('%s.children', $rootAlias), 'child', 'WITH', 'child.enabled = true')
            ->andWhere(sprintf('%s.enabled = :%s', $rootAlias, $enabledParameterName))
            ->andWhere(sprintf('parent.code = :%s', $parentCodeParameterName))
            ->addOrderBy(sprintf('%s.position', $rootAlias))
            ->setParameter($parentCodeParameterName, ($channelMenuTaxon !== null) ? $channelMenuTaxon->getCode() : 'category')
            ->setParameter($enabledParameterName, true)
        ;
    }
}
