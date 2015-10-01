<?php

namespace Sylius\Component\Promotion\Filter;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;

class CheapestFilter extends AbstractFilter
{
    /**
     * {@inheritdoc}
     */
    protected function filter(ArrayCollection $collection)
    {
        if (1 >= $collection->count()) {
            return $collection;
        }

        $criteria = new Criteria();
        $criteria->setMaxResults(1);
        $criteria->orderBy(['unitPrice' => Criteria::ASC]);

        $returnCollection = $collection->matching($criteria);

        return $returnCollection;
    }
}
