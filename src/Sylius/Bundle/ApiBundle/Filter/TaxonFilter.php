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

namespace Sylius\Bundle\ApiBundle\Filter;

use ApiPlatform\Core\Api\IriConverterInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractContextAwareFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

final class TaxonFilter extends AbstractContextAwareFilter
{
    /** @var IriConverterInterface */
    private $iriConverter;

    public function __construct(ManagerRegistry $managerRegistry, IriConverterInterface $iriConverter)
    {
        parent::__construct($managerRegistry);

        $this->iriConverter = $iriConverter;
    }

    public function filterProperty(
        string $property,
        $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null,
        array $context = []
    ) {
        if ($property !== 'taxon') {
            return;
        }

        /** @var TaxonInterface $taxon */
        $taxon = $this->iriConverter->getItemFromIri($value);
        $alias = $queryBuilder->getRootAliases()[0];

        $queryBuilder
            ->distinct()
            ->addSelect('productTaxon')
            ->join(sprintf('%s.productTaxons', $alias), 'p')
            ->innerJoin($alias . '.productTaxons', 'productTaxon')
            ->innerJoin('p.taxon', 'taxon')
            ->andWhere('taxon.left >= :taxonLeft')
            ->andWhere('taxon.right <= :taxonRight')
            ->andWhere('taxon.root = :taxonRoot')
            ->addOrderBy('productTaxon.position')
            ->setParameter('taxonLeft', $taxon->getLeft())
            ->setParameter('taxonRight', $taxon->getRight())
            ->setParameter('taxonRoot', $taxon->getRoot())
        ;
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'taxon' => [
                'type' => 'string',
                'required' => false,
                'property' => null,
                'swagger' => [
                    'name' => 'Product taxon filter',
                    'description' => 'Get a collection of product with chosen taxon',
                ],
            ],
        ];
    }
}
