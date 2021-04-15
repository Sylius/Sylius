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

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractContextAwareFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Core\Bridge\Symfony\Routing\IriConverter;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * It should be replaced with generic collection filter after fixed: https://github.com/api-platform/api-platform/issues/1868
 */
final class TaxonFilter extends AbstractContextAwareFilter
{
    /** @var IriConverter */
    private $iriConverter;

    /** @var ManagerRegistry  */
    protected $managerRegistry;

    public function __construct(IriConverter $iriConverter, ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry);
        $this->iriConverter = $iriConverter;
    }

    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        if ($property !== 'productTaxons') {
            return;
        }

        $taxon = $this->iriConverter->getItemFromIri($value);
        $alias = $queryBuilder->getRootAliases()[0];

        $queryBuilder
            ->join(sprintf('%s.productTaxons', $alias), 'p')
            ->innerJoin('p.taxon', 'taxon')
            ->andWhere('taxon.left >= :taxonLeft')
            ->andWhere('taxon.right <= :taxonRight')
            ->andWhere('taxon.root = :taxonRoot')
            ->setParameter('taxonLeft', $taxon->getLeft())
            ->setParameter('taxonRight', $taxon->getRight())
            ->setParameter('taxonRoot', $taxon->getRoot())
        ;
    }

    public function getDescription(string $resourceClass): array
    {
        $description = [];
        $description["productTaxons"] =
            [
            'type' => 'string',
            'required' => false,
            'property' => null,
            'swagger' => [
                'name' =>'Product taxon filter',
                'description' => 'Get a collection of product with chosen taxon'
            ]
        ];

        return $description;
    }
}
