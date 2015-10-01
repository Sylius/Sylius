<?php

namespace Sylius\Component\Promotion\Filter;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Core\Model\OrderItemInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaxonFilter extends AbstractFilter
{
    const OPTION_TAXON = 'taxon';

    public function __construct($configuration)
    {
        $this->configuration = $this->resolveConfiguration($configuration);
    }

    /**
     * {@inheritdoc}
     */
    protected function filter(ArrayCollection $collection)
    {
        $returnedCollection = new ArrayCollection();

        /** @var OrderItemInterface $item */
        foreach ($collection as $item)
        {
            foreach ($item->getProduct()->getTaxons() as $taxon) {
                if ($taxon->getId() == $this->configuration[self::OPTION_TAXON]) {
                    $returnedCollection->add($item);
                }
            }
        }

        return $returnedCollection;
    }

    private function resolveConfiguration($configuration)
    {
        $resolver = new OptionsResolver();

        $resolver->setRequired(self::OPTION_TAXON);
        $resolver->setAllowedTypes(self::OPTION_TAXON, 'int');

        return $resolver->resolve($configuration);
    }
}