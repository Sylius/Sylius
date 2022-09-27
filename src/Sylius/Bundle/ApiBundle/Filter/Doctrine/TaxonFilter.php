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

namespace Sylius\Bundle\ApiBundle\Filter\Doctrine;

use ApiPlatform\Core\Api\IriConverterInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractContextAwareFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use ApiPlatform\Core\Exception\ItemNotFoundException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

final class TaxonFilter extends AbstractContextAwareFilter
{
    public function __construct(
        ManagerRegistry $managerRegistry,
        private IriConverterInterface $iriConverter,
    ) {
        parent::__construct($managerRegistry);
    }

    public function filterProperty(
        string $property,
        $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null,
        array $context = [],
    ) {
        if ($property !== 'taxon') {
            return;
        }

        try {
            /** @var TaxonInterface $taxon */
            $taxon = $this->iriConverter->getItemFromIri($value);
            $taxonRoot = $taxon->getRoot();
        } catch (InvalidArgumentException|ItemNotFoundException $argumentException) {
            $taxonRoot = null;
        }

        $alias = $queryBuilder->getRootAliases()[0];

        $queryBuilder
            ->distinct()
            ->addSelect('productTaxon')
            ->innerJoin(sprintf('%s.productTaxons', $alias), 'productTaxon')
            ->innerJoin('productTaxon.taxon', 'taxon')
            ->andWhere('taxon.root = :taxonRoot')
            ->setParameter('taxonRoot', $taxonRoot)
        ;

        if (null !== $taxonRoot && empty($context['filters']['order'])) {
            $queryBuilder->addOrderBy('productTaxon.position');
        }
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
