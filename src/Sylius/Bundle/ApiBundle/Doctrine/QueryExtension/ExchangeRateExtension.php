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

namespace Sylius\Bundle\ApiBundle\Doctrine\QueryExtension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\ContextAwareQueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Currency\Model\ExchangeRate;
use Webmozart\Assert\Assert;

final class ExchangeRateExtension implements ContextAwareQueryCollectionExtensionInterface, QueryItemExtensionInterface
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
        if (!is_a($resourceClass, ExchangeRate::class, true)) {
            return;
        }

        $user = $this->userContext->getUser();
        if ($user instanceof AdminUserInterface && in_array('ROLE_API_ACCESS', $user->getRoles(), true)) {
            return;
        }

        Assert::keyExists($context, ContextKeys::CHANNEL);
        $channel = $context[ContextKeys::CHANNEL];

        $currencyParameterName = $queryNameGenerator->generateParameterName('currency');
        $rootAlias = $queryBuilder->getRootAliases()[0];

        $queryBuilder
            ->andWhere(
                $queryBuilder->expr()->orX(
                    sprintf('%s.sourceCurrency = :%s', $rootAlias, $currencyParameterName),
                    sprintf('%s.targetCurrency = :%s', $rootAlias, $currencyParameterName),
                ),
            )
            ->setParameter($currencyParameterName, $channel->getBaseCurrency())
        ;
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, ?string $operationName = null, array $context = [])
    {
        $this->applyToCollection($queryBuilder, $queryNameGenerator, $resourceClass, $operationName, $context);
    }
}
