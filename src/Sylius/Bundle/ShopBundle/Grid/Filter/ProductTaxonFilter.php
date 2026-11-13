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

namespace Sylius\Bundle\ShopBundle\Grid\Filter;

use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\GridBundle\Doctrine\DataSourceInterface as DoctrineDataSourceInterface;
use Sylius\Component\Grid\Data\DataSourceInterface;
use Sylius\Component\Grid\Filtering\FilterInterface;

/**
 * Restricts products to the ones assigned to at least one of the given taxon codes.
 *
 * It uses an "EXISTS" sub-query, so several taxon filters can be combined on the same grid
 * (e.g. "Collection" AND "Product type") without duplicating rows.
 *
 * Options:
 *  - include_all_descendants (bool, default false): also matches products assigned to a descendant of the given taxons.
 *
 * @experimental
 */
final readonly class ProductTaxonFilter implements FilterInterface
{
    public const TYPE = 'shop_product_taxon';

    /**
     * @param class-string $productTaxonClass
     * @param class-string $taxonClass
     */
    public function __construct(
        private string $productTaxonClass,
        private string $taxonClass,
    ) {
    }

    /** @param array<string, mixed> $options */
    public function apply(DataSourceInterface $dataSource, string $name, $data, array $options): void
    {
        $taxonCodes = $this->normalizeTaxonCodes($data);

        if ([] === $taxonCodes) {
            return;
        }

        if (!$dataSource instanceof DoctrineDataSourceInterface) {
            throw new \InvalidArgumentException(sprintf('The "%s" grid filter only supports the Doctrine ORM driver.', self::TYPE));
        }

        $queryBuilder = $dataSource->getQueryBuilder();
        if (!$queryBuilder instanceof QueryBuilder) {
            throw new \InvalidArgumentException(sprintf('The "%s" grid filter only supports the Doctrine ORM driver.', self::TYPE));
        }

        $prefix = 'shop_product_taxon_filter_' . preg_replace('/\W/', '_', $name);
        $productTaxonAlias = $prefix . '_product_taxon';
        $taxonAlias = $prefix . '_taxon';
        $parameterName = $prefix . '_codes';

        $subQueryBuilder = $queryBuilder->getEntityManager()->createQueryBuilder()
            ->select('1')
            ->from($this->productTaxonClass, $productTaxonAlias)
            ->innerJoin(sprintf('%s.taxon', $productTaxonAlias), $taxonAlias)
            ->andWhere(sprintf('%s.product = %s', $productTaxonAlias, $queryBuilder->getRootAliases()[0]))
        ;

        if (true === ($options['include_all_descendants'] ?? false)) {
            $selectedTaxonAlias = $prefix . '_selected_taxon';

            $subQueryBuilder
                ->from($this->taxonClass, $selectedTaxonAlias)
                ->andWhere(sprintf('%s.code IN (:%s)', $selectedTaxonAlias, $parameterName))
                ->andWhere(sprintf('%s.root = %s.root', $taxonAlias, $selectedTaxonAlias))
                ->andWhere(sprintf('%s.left >= %s.left', $taxonAlias, $selectedTaxonAlias))
                ->andWhere(sprintf('%s.right <= %s.right', $taxonAlias, $selectedTaxonAlias))
            ;
        } else {
            $subQueryBuilder->andWhere(sprintf('%s.code IN (:%s)', $taxonAlias, $parameterName));
        }

        $queryBuilder->setParameter($parameterName, $taxonCodes);

        $dataSource->restrict($queryBuilder->expr()->exists($subQueryBuilder->getDQL()));
    }

    /** @return list<string> */
    private function normalizeTaxonCodes(mixed $data): array
    {
        if (is_string($data)) {
            $data = [$data];
        }

        if (!is_array($data)) {
            return [];
        }

        return array_values(array_unique(array_filter(
            $data,
            static fn (mixed $code): bool => is_string($code) && '' !== $code,
        )));
    }
}
