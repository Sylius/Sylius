<?php

namespace Sylius\Component\Promotion\Filter;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PriceFilter extends AbstractFilter
{
    const OPTION_VALUE = 'value';
    const OPTION_COMPARISON = 'comparison';

    public function __construct($configuration)
    {
        $this->configuration = $this->resolveConfiguration($configuration);
    }

    /**
     * {@inheritdoc}
     */
    protected function filter(ArrayCollection $collection)
    {
        $criteria = Criteria::create()->where(
            new Comparison(
                'unitPrice',
                $this->configuration[self::OPTION_COMPARISON],
                $this->configuration[self::OPTION_VALUE]
            )
        );

        return $collection->matching($criteria);
    }

    private function resolveConfiguration($configuration)
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired(
            [
                self::OPTION_VALUE,
                self::OPTION_COMPARISON
            ]
        );
        $resolver->setAllowedTypes(self::OPTION_VALUE, 'int');
        $resolver->setAllowedValues(self::OPTION_COMPARISON,
            [
                Comparison::EQ,
                Comparison::GT,
                Comparison::GTE,
                Comparison::LT,
                Comparison::LTE,
                Comparison::NEQ
            ]
        );

        return $resolver->resolve($configuration);
    }
}