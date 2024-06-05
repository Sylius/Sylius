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

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final readonly class ShopCurrencyExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public function __construct(private SectionProviderInterface $sectionProvider)
    {
    }

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        $this->applyCondition($queryBuilder, $queryNameGenerator, $resourceClass, $context);
    }

    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        ?Operation $operation = null,
        array $context = [],
    ): void {
        $this->applyCondition($queryBuilder, $queryNameGenerator, $resourceClass, $context);
    }

    private function applyCondition(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $context,
    ): void {
        if (!is_a($resourceClass, CurrencyInterface::class, true)) {
            return;
        }

        if (!$this->sectionProvider->getSection() instanceof ShopApiSection) {
            return;
        }

        Assert::keyExists($context, ContextKeys::CHANNEL);
        /** @var ChannelInterface $channel */
        $channel = $context[ContextKeys::CHANNEL];

        $currenciesParameterName = $queryNameGenerator->generateParameterName(':currencies');
        $rootAlias = $queryBuilder->getRootAliases()[0];

        $currencies = array_unique(array_merge(
            $channel->getCurrencies()->toArray(),
            [$channel->getBaseCurrency()],
        ));

        $queryBuilder
            ->andWhere($queryBuilder->expr()->in(sprintf('%s.id', $rootAlias), $currenciesParameterName))
            ->setParameter($currenciesParameterName, $currencies)
        ;
    }
}
