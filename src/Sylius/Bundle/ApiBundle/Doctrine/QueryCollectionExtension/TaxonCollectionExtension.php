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

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Webmozart\Assert\Assert;

final readonly class TaxonCollectionExtension implements QueryCollectionExtensionInterface
{
    public function __construct(private UserContextInterface $userContext)
    {
    }

    /**
     * @param array<array-key, mixed> $context
     */
    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
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
