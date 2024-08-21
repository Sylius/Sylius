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

namespace Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\ProductAssociation;

use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Product\Model\ProductAssociationInterface;
use Webmozart\Assert\Assert;

final readonly class EnabledProductsExtension implements QueryItemExtensionInterface
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
        if (!is_a($resourceClass, ProductAssociationInterface::class, true)) {
            return;
        }

        if (!$this->sectionProvider->getSection() instanceof ShopApiSection) {
            return;
        }

        Assert::keyExists($context, ContextKeys::CHANNEL);

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $enabled = $queryNameGenerator->generateParameterName('enabled');
        $channel = $queryNameGenerator->generateParameterName('channel');

        $queryBuilder->addSelect('associatedProduct');
        $queryBuilder->leftJoin(sprintf('%s.associatedProducts', $rootAlias), 'associatedProduct', 'WITH', sprintf('associatedProduct.enabled = :%s', $enabled));
        $queryBuilder->innerJoin('associatedProduct.channels', 'channel', 'WITH', sprintf('channel = :%s', $channel));
        $queryBuilder->setParameter($enabled, true);
        $queryBuilder->setParameter($channel, $context[ContextKeys::CHANNEL]);
    }
}
