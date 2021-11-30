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

namespace Sylius\Bundle\ApiBundle\Doctrine\QueryCollectionExtension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\ContextAwareQueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class TaxonCollectionExtension implements ContextAwareQueryCollectionExtensionInterface
{
    private UserContextInterface $userContext;

    public function __construct(UserContextInterface $userContext)
    {
        $this->userContext = $userContext;
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null,
        array $context = []
    ): void {
        if (!is_a($resourceClass, TaxonInterface::class, true)) {
            return;
        }

        Assert::keyExists($context, ContextKeys::CHANNEL);
        $channelMenuTaxon = $context[ContextKeys::CHANNEL]->getMenuTaxon();

        $user = $this->userContext->getUser();
        if ($user instanceof AdminUserInterface && in_array('ROLE_API_ACCESS', $user->getRoles(), true)) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder
            ->addSelect('child')
            ->innerJoin($rootAlias . '.parent', 'parent')
            ->leftJoin($rootAlias . '.children', 'child')
            ->andWhere($rootAlias . '.enabled = :enabled')
            ->andWhere('parent.code = :parentCode')
            ->addOrderBy($rootAlias . '.position')
            ->setParameter('parentCode', ($channelMenuTaxon !== null) ? $channelMenuTaxon->getCode() : 'category')
            ->setParameter('enabled', true);
    }
}
