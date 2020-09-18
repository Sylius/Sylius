<?php

declare(strict_types=1);

namespace Sylius\Bundle\ProductBundle\Doctrine\ORM;

use Pagerfanta\Pagerfanta;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Product\Repository\ProductAttributeRepositoryInterface;
use Sylius\Component\Search\Model\SearchQueryInterface;

class ProductAttributeRepository extends EntityRepository implements ProductAttributeRepositoryInterface
{
    public function searchWithoutTerms(): Pagerfanta
    {
        $queryBuilder = $this->createQueryBuilder('o');

        return $this->getPaginator($queryBuilder);
    }

    public function searchByTerms(SearchQueryInterface $query): Pagerfanta
    {
        $queryBuilder = $this->createQueryBuilder('o')
            ->addSelect('translation')
            ->innerJoin('o.translations', 'translation', 'WITH', 'translation.locale = :locale')
            ->where('MATCH_AGAINST(translation.name, :terms) > 0.2')
            ->orWhere('translation.name LIKE :likeTerms')
            ->setParameters([
                'locale' => $query->getLocaleCode(),
                'terms' => $query->getTerms(),
                'likeTerms' => sprintf('%%%s%%', str_replace(' ', '%', $query->getTerms())),
            ])
            ->orderBy('MATCH_AGAINST(translation.name, :terms \'IN BOOLEAN MODE\')', 'DESC')
        ;

        return $this->getPaginator($queryBuilder);
    }
}
