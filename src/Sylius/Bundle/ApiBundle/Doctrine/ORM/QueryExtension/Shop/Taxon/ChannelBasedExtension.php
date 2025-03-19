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

namespace Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Taxon;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Webmozart\Assert\Assert;

final readonly class ChannelBasedExtension implements QueryCollectionExtensionInterface
{
    public function __construct(private SectionProviderInterface $sectionProvider)
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

        if (!$this->sectionProvider->getSection() instanceof ShopApiSection) {
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
            ->andWhere(sprintf('%s.enabled = :%s', $rootAlias, $enabledParameterName)) // TODO: potentially remove from here and apply via a global enabled query extension
            ->andWhere(sprintf('parent.code = :%s', $parentCodeParameterName))
            ->addOrderBy(sprintf('%s.position', $rootAlias))
            ->setParameter($parentCodeParameterName, ($channelMenuTaxon !== null) ? $channelMenuTaxon->getCode() : 'category')
            ->setParameter($enabledParameterName, true)
        ;
    }
}
